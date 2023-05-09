<?php

namespace Modules\Icons\Http\Controllers\IconSets;

use App\Helpers\AdminHelper;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class IconSetController extends Controller
{
    public function index()
    {
        $data = AdminHelper::getInternalLinksUrls([]);

        return view('icons::admin.icon_sets.index', $data);
    }
}
