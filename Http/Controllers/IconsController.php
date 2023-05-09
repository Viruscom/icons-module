<?php

namespace Modules\Icons\Http\Controllers;

use App\Actions\CommonControllerAction;
use App\Helpers\AdminHelper;
use App\Helpers\CacheKeysHelper;
use App\Helpers\FileDimensionHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\MainHelper;
use App\Http\Requests\CategoryPageUpdateRequest;
use App\Models\CategoryPage\CategoryPage;
use App\Models\CategoryPage\CategoryPageTranslation;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Catalogs\Models\Catalog;
use Modules\Catalogs\Models\MainCatalog;
use Modules\Icons\Http\Requests\IconStoreRequest;
use Modules\Icons\Http\Requests\IconUpdateRequest;
use Modules\Icons\Models\Icon;
use Modules\Icons\Models\IconTranslation;

class IconsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        $data = AdminHelper::getInternalLinksUrls([]);

        return view('icons::admin.icons.index', $data);
    }

    public function getEncryptedPath(Request $request)
    {
        return encrypt($request->moduleName . '-' . $request->modelPath . '-' . $request->modelId);
    }


    public function loadIconsPage($path)
    {
        $splitPath = explode("-", decrypt($path));

        $modelClass = $splitPath[1];
        if (!class_exists($modelClass)) {
            return view('icons::admin.icons.error_show');
        } else {
            $modelInstance = new $modelClass;
            $modelConstant = get_class($modelInstance) . '::ALLOW_CATALOGS';
            if (!defined($modelConstant) || !constant($modelConstant)) {
                return view('icons::admin.icons.error_show');
            }

            $model = $modelClass::where('id', $splitPath[2])->first();
            if (is_null($model)) {
                return view('icons::admin.icons.error_show');
            }
            $languages      = LanguageHelper::getActiveLanguages();
            $model['Icons'] = Icon::getCollections($model);

            return view('icons::admin.icons.show', ['moduleName' => $splitPath[0], 'modelPath' => $modelClass, 'model' => $model, 'languages' => $languages]);
        }
    }


    public function create($path)
    {
        $pathHash = $path;
        if ($pathHash == null) {
            return back()->withErrors([trans('icons::admin.icons.page_not_found')]);
        }
        $splitPath = explode("-", decrypt($pathHash));

        $modelClass = $splitPath[1];
        if (!class_exists($modelClass)) {
            return back()->withErrors([trans('icons::admin.icons.page_not_found')]);
        } else {
            $modelInstance = new $modelClass;
            $modelConstant = get_class($modelInstance) . '::ALLOW_ICONS';
            if (!defined($modelConstant) || !constant($modelConstant)) {
                return back()->withErrors([trans('icons::admin.icons.icons_not_allowed')]);
            }

            $model = $modelClass::where('id', $splitPath[2])->first();
            if (is_null($model)) {
                return back()->withErrors([trans('icons::admin.icons.page_not_found')]);
            }

            $data = [
                'languages'     => LanguageHelper::getActiveLanguages(),
                'fileRulesInfo' => Icon::getUserInfoMessage(),
                'path'          => $pathHash,
            ];
            $data = AdminHelper::getInternalLinksUrls($data);

            return view('icons::admin.icons.create', $data);
        }
    }

    public function store(IconStoreRequest $request, CommonControllerAction $action)
    {
        $splitPath  = explode("-", decrypt($request->path));
        $modelClass = $splitPath[1];
        if (!class_exists($modelClass)) {
            return redirect()->back()->withErrors(['icons::admin.icons.page_not_found']);
        }

        if ($request->has('image')) {
            $request->validate(['image' => FileDimensionHelper::getRules('Icons', 1)], FileDimensionHelper::messages('Icons', 1));
        }
        $icon = $action->doSimpleCreate(Icon::class, $request);
        $icon->storeAndAddNew($request);

        return redirect()->route('admin.icons.manage.load-icons', ['path' => $request->path])->with('success-message', trans('admin.common.successful_create'));
    }

    public function edit($id)
    {
        $icon = Icon::whereId($id)->with('translations')->first();
        MainHelper::goBackIfNull($icon);

        $data = [
            'icon'          => $icon,
            'languages'     => LanguageHelper::getActiveLanguages(),
            'fileRulesInfo' => Icon::getUserInfoMessage()
        ];
        $data = AdminHelper::getInternalLinksUrls($data);

        return view('icons::admin.icons.edit', $data);
    }

    public function update($id, IconUpdateRequest $request, CommonControllerAction $action): RedirectResponse
    {
        $icon = Icon::whereId($id)->with('translations')->first();
        MainHelper::goBackIfNull($icon);

        $request['path'] = encrypt($icon->module . '-' . $icon->model . '-' . $icon->model_id);
        $action->doSimpleUpdate(Icon::class, IconTranslation::class, $icon, $request);

        if ($request->has('image')) {
            $request->validate(['image' => FileDimensionHelper::getRules('Icons', 1)], FileDimensionHelper::messages('Icons', 1));
            $icon->saveFile($request->image);
        }

        return redirect()->route('admin.icons.manage.load-icons', ['path' => $request->path])->with('success-message', 'admin.common.successful_edit');
    }

    public function deleteMultiple(Request $request, CommonControllerAction $action): RedirectResponse
    {
        if (!is_null($request->ids[0])) {
            $ids = array_map('intval', explode(',', $request->ids[0]));
            foreach ($ids as $id) {
                $icon = Icon::find($id);
                if (is_null($icon)) {
                    continue;
                }

                if ($icon->existsFile($icon->filename)) {
                    $icon->deleteFile($icon->filename);
                }

                $modelsToUpdate = Icon::where('module', $icon->module)->where('model', $icon->model)->where('model_id', $icon->model_id)->where('main_position', $icon->main_position)->where('position', '>', $icon->position)->get();
                $icon->delete();
                foreach ($modelsToUpdate as $modelToUpdate) {
                    $modelToUpdate->update(['position' => $modelToUpdate->position - 1]);
                }
            }

            return redirect()->back()->with('success-message', 'admin.common.successful_delete');
        }

        return redirect()->back()->withErrors(['admin.common.no_checked_checkboxes']);
    }
    public function delete($id): RedirectResponse
    {
        $icon = Icon::where('id', $id)->first();
        MainHelper::goBackIfNull($icon);

        $modelsToUpdate = Icon::where('module', $icon->module)->where('model', $icon->model)->where('model_id', $icon->model_id)->where('main_position', $icon->main_position)->where('position', '>', $icon->position)->get();
        $icon->delete();
        foreach ($modelsToUpdate as $currentModel) {
            $currentModel->update(['position' => $currentModel->position - 1]);
        }

        return redirect()->back()->with('success-message', 'admin.common.successful_delete');
    }

    public function activeMultiple($active, Request $request, CommonControllerAction $action): RedirectResponse
    {
        $action->activeMultiple(Icon::class, $request, $active);

        return redirect()->back()->with('success-message', 'admin.common.successful_edit');
    }
    public function active($id, $active): RedirectResponse
    {
        $icon = Icon::find($id);
        MainHelper::goBackIfNull($icon);

        $icon->update(['active' => $active]);

        return redirect()->back()->with('success-message', 'admin.common.successful_edit');
    }

    public function positionUp($id, CommonControllerAction $action): RedirectResponse
    {
        $icon = Icon::whereId($id)->first();
        MainHelper::goBackIfNull($icon);

        $previousModel = Icon::where('module', $icon->module)->where('model', $icon->model)->where('model_id', $icon->model_id)->where('main_position', $icon->main_position)->where('position', $icon->position - 1)->first();
        if (!is_null($previousModel)) {
            $previousModel->update(['position' => $previousModel->position + 1]);
            $icon->update(['position' => $icon->position - 1]);
        }

        return redirect()->back()->with('success-message', 'admin.common.successful_edit');
    }

    public function positionDown($id, CommonControllerAction $action): RedirectResponse
    {
        $icon = Icon::whereId($id)->first();
        MainHelper::goBackIfNull($icon);

        $nextModel = Icon::where('module', $icon->module)->where('model', $icon->model)->where('model_id', $icon->model_id)->where('main_position', $icon->main_position)->where('position', $icon->position + 1)->first();
        if (!is_null($nextModel)) {
            $nextModel->update(['position' => $nextModel->position - 1]);
            $icon->update(['position' => $icon->position + 1]);
        }

        return redirect()->back()->with('success-message', 'admin.common.successful_edit');
    }
}
