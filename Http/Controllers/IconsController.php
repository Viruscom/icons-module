<?php

namespace Modules\Icons\Http\Controllers;

use App\Actions\CommonControllerAction;
use App\Helpers\AdminHelper;
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


    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('icons::admin.icons.create', [
            'languages'     => LanguageHelper::getActiveLanguages(),
            'fileRulesInfo' => Icon::getUserInfoMessage()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Renderable
     */
    public function store(Request $request, CommonControllerAction $action)
    {
        $splitPath  = explode("-", decrypt($request->path));
        $modelClass = $splitPath[1];
        if (!class_exists($modelClass)) {
            return redirect()->back()->withErrors(['icons::admin.icons.warning_class_not_found']);
        }

        $catalog = $action->doSimpleCreate(Icon::class, $request);
        $catalog->storeAndAddNew($request);
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
