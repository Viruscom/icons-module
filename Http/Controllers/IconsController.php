<?php

namespace Modules\Icons\Http\Controllers;

use App\Actions\CommonControllerAction;
use App\Helpers\AdminHelper;
use App\Helpers\FileDimensionHelper;
use App\Helpers\LanguageHelper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Catalogs\Models\Catalog;
use Modules\Icons\Models\Icon;

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

            return view('icons::admin.icons.create', [
                'languages'     => LanguageHelper::getActiveLanguages(),
                'fileRulesInfo' => Icon::getUserInfoMessage(),
                'path'          => $pathHash,
            ]);
        }
    }

    public function store(Request $request, CommonControllerAction $action)
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

    /**
     * Show the specified resource.
     *
     * @param int $id
     *
     * @return Renderable
     */
    public function show($id)
    {
        return view('icons::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Renderable
     */
    public function edit($id)
    {
        return view('icons::admin.icons.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     *
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
