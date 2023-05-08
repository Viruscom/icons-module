<?php

namespace Modules\Icons\Models;

use App\Helpers\AdminHelper;
use App\Helpers\FileDimensionHelper;
use App\Interfaces\Models\CommonModelInterface;
use App\Interfaces\Models\ImageModelInterface;
use App\Traits\CommonActions;
use App\Traits\Scopes;
use App\Traits\StorageActions;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Catalogs\Models\Catalog;
use Modules\Team\Models\TeamDivision;
use Modules\Team\Models\TeamTranslation;

class Icon extends Model implements TranslatableContract, CommonModelInterface, ImageModelInterface
{
    use Translatable, StorageActions, Scopes, CommonActions;

    public const FILES_PATH                           = "icons";
    const        ICONS_AFTER_DESCRIPTION              = 0;
    const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_1 = 1;
    const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_2 = 2;
    const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_3 = 3;
    const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_4 = 4;
    const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_5 = 5;
    const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_6 = 6;

    public static string $ICON_SYSTEM_IMAGE  = 'icon_img.png';
    public static string $ICON_RATIO         = '1/1';
    public static string $ICON_MIMES         = 'jpg,jpeg,png,gif';
    public static string $ICON_MAX_FILE_SIZE = '3000';

    protected static $API_BASE_URL = 'https://common.citysofia.com';
    protected static $API_TOKEN    = '$2y$10$fQjaM8pjZDY5qfsAQVjuK.Gg2QbnNNfilIFdxCU2dkBXpUULXJrom';

    public array $translatedAttributes = ['short_description'];
    protected    $table                = "icons";
    protected    $fillable             = ['module', 'model', 'model_id', 'icon_set_id', 'active', 'main_position', 'position', 'filename'];
    public static function getCollections($parentModel): array
    {
        return [
            self::ICONS_AFTER_DESCRIPTION              => self::getIcons($parentModel, self::ICONS_AFTER_DESCRIPTION),
            self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_1 => self::getIcons($parentModel, self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_1),
            self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_2 => self::getIcons($parentModel, self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_2),
            self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_3 => self::getIcons($parentModel, self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_3),
            self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_4 => self::getIcons($parentModel, self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_4),
            self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_5 => self::getIcons($parentModel, self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_5),
            self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_6 => self::getIcons($parentModel, self::ICONS_AFTER_ADDITIONAL_DESCRIPTION_6),
        ];
    }

    public static function getIcons($parentModel, $mainPosition)
    {
        return Icon::where('model', get_class($parentModel))
            ->where('model_id', $parentModel->id)
            ->where('main_position', $mainPosition)->with('translations')->orderBy('position')->get();
    }


    public static function getIconSets()
    {
        $attributes = [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . self::$API_TOKEN
            ]
        ];

        $http     = new Client();
        $response = $http->get(self::$API_BASE_URL . '/api/icon_sets/getAll', $attributes);

        return $response->getBody()->getContents();
    }

    public static function getIconsFromSet($setId)
    {
        $attributes = [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . self::$API_TOKEN
            ]
        ];

        $http     = new Client();
        $response = $http->get(self::$API_BASE_URL . '/api/icon_sets/get/' . $setId, $attributes);

        return $response->getBody()->getContents();
    }
    public static function getFileRules(): string
    {
        return FileDimensionHelper::getRules('Icons', 1);
    }
    public static function getUserInfoMessage(): string
    {
        return FileDimensionHelper::getUserInfoMessage('Icons', 1);
    }
    public function getSystemImage(): string
    {
        return AdminHelper::getSystemImage(self::$ICON_SYSTEM_IMAGE);
    }
    public function setKeys($array): array
    {
        $array[1]['sys_image_name'] = trans('icons::admin.icons.index');
        $array[1]['sys_image']      = self::$ICON_SYSTEM_IMAGE;
        $array[1]['sys_image_path'] = AdminHelper::getSystemImage(self::$ICON_SYSTEM_IMAGE);
        $array[1]['ratio']          = self::$ICON_RATIO;
        $array[1]['mimes']          = self::$ICON_MIMES;
        $array[1]['max_file_size']  = self::$ICON_MAX_FILE_SIZE;
        $array[1]['file_rules']     = 'mimes:' . self::$ICON_MIMES . '|size:' . self::$ICON_MAX_FILE_SIZE . '|dimensions:ratio=' . self::$ICON_RATIO;

        return $array;
    }

    public function getFilepath($filename): string
    {
        return $this->getFilesPath() . $filename;
    }
    public function getFilesPath(): string
    {
        return self::FILES_PATH . '/' . $this->id . '/';
    }
    public static function getRequestData($request): array
    {
        $splitPath = explode("-", decrypt($request->path));
        if (is_null($request->position)) {
            $request['position'] = self::generatePosition($request);
        }

        $data = [
            'module'        => $splitPath[0],
            'model'         => $splitPath[1],
            'model_id'      => $splitPath[2],
            'main_position' => $request->main_position,
            'position'      => $request->position,
            'icon_set_id'   => $request->icon_set_id
        ];

        $data['active'] = true;
        if ($request->has('active')) {
            $data['active'] = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
        }

        if ($request->has('filename')) {
            $data['filename'] = $request->filename;
        }

        return $data;
    }
    public static function generatePosition($request): int
    {
        return 1;

        //        $galleries = self::where('parent_type_id', $parentTypeId)->where('parent_id', $parentId)->where('main_position', $request->main_position)->orderBy('position', 'desc')->get();
        //        if (count($galleries) < 1) {
        //            return 1;
        //        }
        //        if (!$request->has('position') || is_null($request['position'])) {
        //            return $galleries->first()->position + 1;
        //        }
        //
        //        if ($request['position'] > $galleries->first()->position) {
        //            return $galleries->first()->position + 1;
        //        }
        //
        //        $galleriesUpdate = self::where('parent_type_id', $parentTypeId)->where('parent_id', $parentId)->where('main_position', $request->main_position)->where('position', '>=', $request['position'])->get();
        //        self::updateGalleyPosition($galleriesUpdate, true);
        //
        //        return $request['position'];
    }
    public static function getLangArraysOnStore($data, $request, $languages, $modelId, $isUpdate)
    {
        foreach ($languages as $language) {
            $data[$language->code] = IconTranslation::getLanguageArray($language, $request, $modelId, $isUpdate);
        }

        return $data;
    }
}
