<?php

namespace Modules\Icons\Models;

use App\Helpers\AdminHelper;
use App\Helpers\FileDimensionHelper;
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

class Icon extends Model implements TranslatableContract
{
    use Translatable, StorageActions, Scopes;

    const ICONS_AFTER_DESCRIPTION              = "iconsAfterDescription";
    const ICONS_AFTER_ADDITIONAL_DESCRIPTION_1 = "iconsAfterAdditionalDescription_1";
    const ICONS_AFTER_ADDITIONAL_DESCRIPTION_2 = "iconsAfterAdditionalDescription_2";
    const ICONS_AFTER_ADDITIONAL_DESCRIPTION_3 = "iconsAfterAdditionalDescription_3";
    const ICONS_AFTER_ADDITIONAL_DESCRIPTION_4 = "iconsAfterAdditionalDescription_4";
    const ICONS_AFTER_ADDITIONAL_DESCRIPTION_5 = "iconsAfterAdditionalDescription_5";
    const ICONS_AFTER_ADDITIONAL_DESCRIPTION_6 = "iconsAfterAdditionalDescription_6";

    public static string $ICON_SYSTEM_IMAGE  = 'icon_1_image.png';
    public static string $ICON_RATIO         = '1/1';
    public static string $ICON_MIMES         = 'jpg,jpeg,png,gif';
    public static string $ICON_MAX_FILE_SIZE = '3000';

    protected static $API_BASE_URL = 'https://common.citysofia.com';
    protected static $API_TOKEN    = '$2y$10$fQjaM8pjZDY5qfsAQVjuK.Gg2QbnNNfilIFdxCU2dkBXpUULXJrom';

    public array $translatedAttributes = ['short_description'];
    protected    $table                = "icons";
    protected    $fillable             = ['parent_type_id', 'parent_id', 'icon_set_id', 'active', 'main_position', 'position', 'creator_user_id', 'filename'];

    public static string $IMAGES_PATH = "images/icons";
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
            ->where('main_position', $mainPosition)->with('translations', 'parent', 'parent.translations')->orderBy('position')->get();
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

}
