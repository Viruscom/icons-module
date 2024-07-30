<?php

    namespace Modules\Icons\Models;

    use App\Helpers\AdminHelper;
    use App\Helpers\FileDimensionHelper;
    use App\Interfaces\Models\CommonModelInterface;
    use App\Interfaces\Models\ImageModelInterface;
    use App\Traits\CommonActions;
    use App\Traits\HasModelRatios;
    use App\Traits\Scopes;
    use App\Traits\StorageActions;
    use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
    use Astrotomic\Translatable\Translatable;
    use GuzzleHttp\Client;
    use Illuminate\Database\Eloquent\Model;

    class Icon extends Model implements TranslatableContract, CommonModelInterface, ImageModelInterface
    {
        use Translatable, StorageActions, Scopes, CommonActions, HasModelRatios;

        public const FILES_PATH                           = "icons";
        const        ICONS_AFTER_DESCRIPTION              = 0;
        const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_1 = 1;
        const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_2 = 2;
        const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_3 = 3;
        const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_4 = 4;
        const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_5 = 5;
        const        ICONS_AFTER_ADDITIONAL_DESCRIPTION_6 = 6;

        public static string $ICON_SYSTEM_IMAGE = 'icons_1_image.png';

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
            if (is_null($parentModel)) {
                return null;
            }

            return Icon::where('model', get_class($parentModel))
                ->where('model_id', $parentModel->id)
                ->where('main_position', $mainPosition)->where('active', true)->with('translations')->orderBy('position')->get();
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

            if ($request->hasFile('image')) {
                $data['filename'] = pathinfo(CommonActions::getValidFilenameStatic($request->image->getClientOriginalName()), PATHINFO_FILENAME) . '.' . $request->image->getClientOriginalExtension();
            }

            return $data;
        }

        public static function generatePosition($request): int
        {
            $splitPath = explode("-", decrypt($request->path));

            $icons = self::where('module', $splitPath[0])
                ->where('model', $splitPath[1])
                ->where('model_id', $splitPath[2])
                ->where('main_position', $request->main_position)->orderBy('position', 'desc')->get();
            if (count($icons) < 1) {
                return 1;
            }
            if (!$request->has('position') || is_null($request['position'])) {
                return $icons->first()->position + 1;
            }

            if ($request['position'] > $icons->first()->position) {
                return $icons->first()->position + 1;
            }

            $iconsUpdate = self::where('module', $splitPath[0])
                ->where('model', $splitPath[1])
                ->where('model_id', $splitPath[2])
                ->where('main_position', $request->main_position)->where('position', '>=', $request['position'])->get();
            self::updateIconsPosition($iconsUpdate, true);

            return $request['position'];
        }

        private static function updateIconsPosition($icons, $increment = true): void
        {
            foreach ($icons as $iconUpdate) {
                $position = ($increment) ? $iconUpdate->position + 1 : $iconUpdate->position - 1;
                $iconUpdate->update(['position' => $position]);
            }
        }

        public static function getLangArraysOnStore($data, $request, $languages, $modelId, $isUpdate)
        {
            foreach ($languages as $language) {
                $data[$language->code] = IconTranslation::getLanguageArray($language, $request, $modelId, $isUpdate);
            }

            return $data;
        }

        public function setKeys($array): array
        {
            $array[1]['sys_image_name'] = trans('icons::admin.icons.index');
            $array[1]['sys_image']      = self::$ICON_SYSTEM_IMAGE;
            $array[1]['sys_image_path'] = AdminHelper::getSystemImage(self::$ICON_SYSTEM_IMAGE);
            $array[1]['field_name']     = 'icons';
            $array[1]['ratio']          = self::getModelRatio('icons');
            $array[1]['mimes']          = self::getModelMime('icons');
            $array[1]['max_file_size']  = self::getModelMaxFileSize('icons');
            $array[1]['file_rules']     = 'mimes:' . self::getModelMime('icons') . '|size:' . self::getModelMaxFileSize('icons') . '|dimensions:ratio=' . self::getModelRatio('icons');

            return $array;
        }

        public function getSystemImage(): string
        {
            return AdminHelper::getSystemImage(self::$ICON_SYSTEM_IMAGE);
        }

        public function getFilepath($filename): string
        {
            return $this->getFilesPath() . $filename;
        }

        public function getFilesPath(): string
        {
            return self::FILES_PATH . '/' . $this->id . '/';
        }
    }
