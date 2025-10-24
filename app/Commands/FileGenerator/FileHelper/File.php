<?php

namespace App\Console\Commands\FileGenerator\FileHelper;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDO;

/**
 * Class File
 * @package App\Libs\Cmd
 */
class File extends Command
{
    /**
     * @return string[]
     */
    protected function getExcerptColumns()
    {
        return [];
    }

    /**
     * Create model
     * @param string $sdk
     * @param string $module
     * @param string $model
     * @param string $table
     * @return string
     * @throws Exception
     */
    public function createModel(string $model, string $table)
    {
        $modelName = Str::ucfirst($model);
        $modelPath = base_path("app/Models");

        $namespace = 'App\\Models';

        if (! file_exists($modelPath)) {
            mkdir($modelPath, 0775, true);
        }

        [$schema, $types, $primaryKey] = (new Scaffold())->getSchema($table);

        if (! empty($primaryKey)) {
            unset($schema[array_keys($schema, $primaryKey)[0]]);
        }

        $fillable = implode(",\n" . str_repeat(' ', 8), array_map(function ($field) {
            return '\'' . $field . '\'';
        }, array_diff(
            $schema,
            array_merge($this->getExcerptColumns(), ['created_at', 'updated_at', 'deleted_at'])
        ))) . ' ,';

        $properties = [];
        foreach ($types as $field => $type) {
            $properties[] = <<<CONTENT
 * @property $type $$field
CONTENT;
        }

        $properties = implode("\n", $properties);

        $modelFile = $modelPath .'/'. $modelName . '.php';

        if (! file_exists($modelFile)) {
            $content = <<<CONTENT
<?php

namespace $namespace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class $modelName
 * @package $namespace
 */

/**
 * [auto-gen-property]
$properties
 * [/auto-gen-property]
 *
 */
class $modelName extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string \$table
     */
    protected \$table = '$table';

    /**
     * The primary key for the model.
     *
     * @var string \$primaryKey
     */
    protected \$primaryKey = '$primaryKey';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected \$fillable = [
        # [auto-gen-attribute]
        $fillable
        # [/auto-gen-attribute]
    ];
}

CONTENT;
            // Create model file
            $file = fopen($modelFile, 'w');
            fwrite($file, $content);
            fclose($file);

            $ret = 'Model "\\' . $namespace . '\\' . $modelName . '" was created!';
        } else {
            $content = file_get_contents($modelFile);
            $pregReplace = '~\[auto-gen-property\](.*?)(.|[\r\n\t])*?\[\/auto-gen-property\]~';
            $content = preg_replace_callback_array(
                [
                    $pregReplace => function ($match) use ($properties) {
                        return "[auto-gen-property]\n$properties\n * [/auto-gen-property]";
                    }
                ],
                $content
            );

            // Update model file
            $file = fopen($modelFile, 'w');
            fwrite($file, $content);
            fclose($file);

            $ret = 'Model "\\' . $namespace . '\\' . $modelName . '" was updated!';
        }

        return $ret;
    }

    /**
     * Create repository
     * @param string $sdk
     * @param string $module
     * @param string $repository
     * @return string
     * @throws Exception
     */
    public function createRepository(string $repository)
    {
        $repositoryName = Str::ucfirst($repository) . 'Repository';
        $repositoryEloquent = Str::ucfirst($repository) . 'RepositoryEloquent';
        $repositoryInterfacePath = base_path("app/Repositories/$repository/");
        $repositoryNamespace =  "App\\Repositories\\$repository";
        $repositoryClass = $repositoryNamespace . '\\' . $repositoryName;
        $classModel =  'App\\Models\\' .$repository;

        if (! file_exists($repositoryInterfacePath)) {
            mkdir($repositoryInterfacePath, 0775, true);
        }

        if (! class_exists($classModel)) {
            throw new Exception('Model class has not existed!');
        }

        if (class_exists($repositoryClass)) {
            throw new Exception('Repository has already existed!');
        }



        $repositoryEloquentContent = <<<CONTENT
<?php

namespace $repositoryNamespace;

use App\Repositories\BaseRepository;
use $classModel;

/**
 * Class $repositoryName
 * @package $repositoryNamespace
 */
class $repositoryEloquent extends BaseRepository implements $repositoryName
{
    /**
     * Map model
     * @return mixed
     */
    public function model()
    {
        return $repository::class;
    }
}

CONTENT;

        $repositoryInterfaceContent = <<<CONTENT
<?php

namespace $repositoryNamespace;

use App\Repositories\RepositoryInterface;

interface $repositoryName extends RepositoryInterface
{
}

CONTENT;

        // Create repository file
        $file = fopen($repositoryInterfacePath .'/'. $repositoryEloquent . '.php', 'w');
        fwrite($file, $repositoryEloquentContent);
        fclose($file);

        // Create repository file
        $file = fopen($repositoryInterfacePath .'/'. $repositoryName . '.php', 'w');
        fwrite($file, $repositoryInterfaceContent);
        fclose($file);


        return 'Repository "\\' . $repositoryClass . '" was created!';
    }

    /**
     * @param string $module
     * @param string $request
     * @param string $table
     * @return string
     * @throws Exception
     */
    public function createRequest(string $module, string $request, string $table)
    {
        $requestName = Str::ucfirst($request) . 'Request';
        $moduleName = Str::ucfirst($module);
        $tableName = trim($table);

        $requestPath = base_path('app/Http/Requests/'.$moduleName);
        $namespace = "App\\Http\\Requests\\$moduleName";

        if (! file_exists($requestPath)) {
            mkdir($requestPath, 0775, true);
        }

        $properties = [];
        $attributes = [];

        $resource = DB::connection()->getPdo()->prepare("DESCRIBE `{$tableName}`");
        $resource->execute();

        if ($resource->rowCount() > 0) {
            $types = [];

            while ($row = $resource->fetch(PDO::FETCH_ASSOC)) {
                $field = $row['Field'];
                $type = $row['Type'];
                $types[] = $type;
                !($row['Null'] == 'NO') or $notNullAble[] = $field;

                $realType = $this->getRealType($type);
                $properties[] = <<<CONTENT
 * @property $realType $$field
CONTENT;

                $attributes[] = "'$field' => ''";

                if (in_array($field, $this->getExcerptColumns())) {
                    continue;
                }
            }

            $properties = implode("\n", $properties);
            $attributes = implode(",\n" . str_repeat(' ', 12), $attributes);
        }

        // Create model file
        $requestFile = $requestPath .'/'. $requestName . '.php';

        if (file_exists($requestFile)) {
            throw new Exception('Request existed, please update manually');
        }

        if (! file_exists($requestFile)) {
            $content = <<<CONTENT
<?php

namespace $namespace;

use App\Http\Requests\FormRequest;

/**
 * [auto-gen-property]
$properties
 * [/auto-gen-property]
 */
class $requestName extends FormRequest
{
    /**
     * @return string[]
     */
    public function attributes()
    {
        return [
            $attributes
        ];
    }
}
CONTENT;

            $file = fopen($requestFile, 'w');
            fwrite($file, $content);
            fclose($file);

            $ret = 'Request "\\' . $namespace . '\\' . $requestName . '" was created!';
        } else {
            throw new Exception("{$requestFile} existed, please update manually");

//            $content = file_get_contents($requestFile);
//            $content = preg_replace_callback_array(
//                [
//                    '~\[auto-gen-property\](.*?)(.|[\r\n\t])*?\[\/auto-gen-property\]~' => function($match) use($properties) {
//                        return "[auto-gen-property]\n$properties\n * [/auto-gen-property]";
//                    },
//                    '~\#\[auto-gen-data-map\](.*?)(.|[\r\n\t])*?\[\/auto-gen-data-map\]~' => function($match) use($dataMap) {
//                        return "#[auto-gen-data-map]\n\t\t$dataMap\n\t\t# [/auto-gen-data-map]";
//                    }
//                ],
//                $content
//            );
//
//            $file = fopen($requestFile, 'w');
//            fwrite($file, $content);
//            fclose($file);
//
//            $ret = 'Request "\\' . $namespace . '\\' . $requestName . '" was updated!';
        }

        return $ret;
    }

    /**
     * @param string $module
     * @param string $request
     * @param string $table
     * @return string
     * @throws Exception
     */
    public function createRequestStore(string $module, string $request, string $table)
    {
        $requestName = Str::ucfirst($request) . 'Request';
        $requestStoreName = Str::ucfirst($request) . 'StoreRequest';
        $moduleName = Str::ucfirst($module);
        $tableName = trim($table);

        $requestPath = base_path('app/Http/Requests/'.$moduleName);
        $namespace = "App\\Http\\Requests\\$moduleName";

        if (! file_exists($requestPath)) {
            mkdir($requestPath, 0775, true);
        }

        if (! file_exists(app_path("app/Http/Requests/" . $requestStoreName . '.php'))) {
            $this->createRequest($module, $request, $table);
        }

        $rules = [];

        $resource = DB::connection()->getPdo()->prepare("DESCRIBE `{$tableName}`");
        $resource->execute();

        if ($resource->rowCount() > 0) {
            $types = [];
            while ($row = $resource->fetch(PDO::FETCH_ASSOC)) {
                $field = $row['Field'];
                $type = $row['Type'];
                $types[] = $type;
                !($row['Null'] == 'NO') or $notNullAble[] = $field;

                if (in_array($field, $this->getExcerptColumns())) {
                    continue;
                }

                if ($row['Key'] == 'PRI') {
                    continue;
                }

                $rule = [];

                if ($row['Null'] != 'NO') {
                    $rule[] = 'nullable';
                } elseif (is_null($row['Default'])) {
                    $rule[] = 'required';
                }

                if (preg_match('/^(bigint|int|integer|mediumint|smallint|tinyint)(\(([\d]+)\))?/', $type, $matches)) {
                    $rule[] = 'integer';
                    $rules[] = "'$field' => '".implode('|', $rule)."'";
                    continue;
                }

                if (preg_match('/^(numeric|real|decimal|double|float)(\(([\d]+),([\d]+)\))?/', $type, $matches)) {
                    $rule[] = 'numeric';
                    $rules[] = "'$field' => '".implode('|', $rule)."'";
                    continue;
                }

                if (preg_match('/^(longtext|text|mediumtext|tinytext|varchar|char|enum|set|varbinary|year|date|datetime|time|timestamp)(\(([\d]+)\))?/', $type, $matches)) {
                    $rule[] = 'string';
                    if (isset($matches[3]) && $matches[3] > 0) {
                        $rule[] = 'max:' .  $matches[3];
                    }

                    $rules[] = "'$field' => '".implode('|', $rule)."'";
                    continue;
                }

                $rules[] = "'$field' => ''";
            }
        }

        $rules = implode(",\n" . str_repeat(' ', 12), $rules);

        $requestFile = $requestPath .'/'. $requestStoreName . '.php';
        if (! file_exists($requestFile)) {
            $content = <<<CONTENT
<?php

namespace $namespace;

class $requestStoreName extends $requestName
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            # [auto-gen-rules]
            $rules
            # [/auto-gen-rules]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            # messages
        ];
    }
}
CONTENT;

            // Create request file
            $file = fopen($requestPath .'/'. $requestStoreName . '.php', 'w');
            fwrite($file, $content);
            fclose($file);

            return 'Request "\\' . $namespace . '\\' . $requestStoreName . '" was created!';
        } else {
            throw new Exception("{$requestFile} existed, please update manually");

//            $content = file_get_contents($requestFile);
//            $content = preg_replace_callback_array(
//                [
//                    '~\#\[auto-gen-rules\](.*?)(.|[\r\n\t])*?\[\/auto-gen-rules\]~' => function($match) use($rules) {
//                        return "# [auto-gen-rules]\n\t\t\t$rules\n\t\t\t# [/auto-gen-rules]";
//                    }
//                ],
//                $content
//            );
//
//            // Update request file
//            $file = fopen($requestPath .'/'. $requestStoreName . '.php', 'w');
//            fwrite($file, $content);
//            fclose($file);
//
//            return 'Request "\\' . $namespace . '\\' . $requestStoreName . '" was updated!';
        }
    }

    /**
     * @param string $module
     * @param string $request
     * @param string $table
     * @return string
     * @throws Exception
     */
    public function createRequestUpdate(string $module, string $request, string $table)
    {
        $requestName = Str::ucfirst($request);
        $requestStoreName = $requestName . 'StoreRequest';
        $requestUpdateName = $requestName . 'UpdateRequest';
        $moduleName = Str::ucfirst($module);
        $tableName = trim($table);

        $requestPath = base_path('app/Http/Requests/'.$moduleName.'/');
        $namespace = "App\\Http\\Requests\\$moduleName";

        if (! file_exists($requestPath)) {
            mkdir($requestPath, 0775, true);
        }

        if (! file_exists(base_path("app/Http/Requests/" . $requestStoreName . '.php'))) {
            $this->createRequestStore($module, $request, $table);
        }

        $rules = [];

        $resource = DB::connection()->getPdo()->prepare("DESCRIBE `{$tableName}`");
        $resource->execute();

        if ($resource->rowCount() > 0) {
            $types = [];
            while ($row = $resource->fetch(PDO::FETCH_ASSOC)) {
                $field = $row['Field'];
                $type = $row['Type'];
                $types[] = $type;
                !($row['Null'] == 'NO') or $notNullAble[] = $field;

                if (in_array($field, $this->getExcerptColumns())) {
                    continue;
                }

                $rule = [];
                if ($row['Key'] != 'PRI') {
                    if ($row['Null'] != 'NO' || !is_null($row['Default'])) {
                        $rule[] = 'nullable';
                    }
                } else {
                    if (is_null($row['Default'])) {
                        $rule[] = 'required';
                    }
                }

                if (preg_match('/^(bigint|int|integer|mediumint|smallint|tinyint)(\(([\d]+)\))?/', $type, $matches)) {
                    $rule[] = 'integer';
                    $rules[] = "'$field' => '".implode('|', $rule)."'";
                    continue;
                }

                if (preg_match('/^(numeric|real|decimal|double|float)(\(([\d]+),([\d]+)\))?/', $type, $matches)) {
                    $rule[] = 'numeric';
                    $rules[] = "'$field' => '".implode('|', $rule)."'";
                    continue;
                }

                if (preg_match('/^(longtext|text|mediumtext|tinytext|varchar|char|enum|set|varbinary|year|date|datetime|time|timestamp)(\(([\d]+)\))?/', $type, $matches)) {
                    $rule[] = 'string';
                    if (isset($matches[3]) && $matches[3] > 0) {
                        $rule[] = 'max:' .  $matches[3];
                    }

                    $rules[] = "'$field' => '".implode('|', $rule)."'";
                    continue;
                }

                $rules[] = "'$field' => ''";
            }
        }

        $rules = implode(",\n" . str_repeat(' ', 12), $rules);

        $requestFile = $requestPath .'/'. $requestUpdateName . '.php';
        if (! file_exists($requestFile)) {
            $content = <<<CONTENT
<?php

namespace $namespace;

class $requestUpdateName extends $requestStoreName
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            # [auto-gen-rules]
            $rules
            # [/auto-gen-rules]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
            # messages
        ]);
    }
}
CONTENT;

            // Create request file
            $file = fopen($requestPath .'/'. $requestUpdateName . '.php', 'w');
            fwrite($file, $content);
            fclose($file);

            return 'Request "\\' . $namespace . '\\' . $requestUpdateName . '" was created!';
        } else {
            throw new Exception("{$requestFile} existed, please update manually");

//            $content = file_get_contents($requestFile);
//            $content = preg_replace_callback_array(
//                [
//                    '~\#\[auto-gen-rules\](.*?)(.|[\r\n\t])*?\[\/auto-gen-rules\]~' => function($match) use($rules) {
//                        return "# [auto-gen-rules]\n\t\t\t$rules\n\t\t\t# [/auto-gen-rules]";
//                    }
//                ],
//                $content
//            );
//
//            // Update request file
//            $file = fopen($requestPath .'/'. $requestUpdateName . '.php', 'w');
//            fwrite($file, $content);
//            fclose($file);
//
//            return 'Request "\\' . $namespace . '\\' . $requestUpdateName . '" was updated!';
        }
    }

    /**
     * Create API resource
     * @param string $module
     * @param string $resource
     * @param string $table
     * @return string
     * @throws Exception
     *
     */
    protected function getRealType($type)
    {
        $intMatch = '/^(bigint|int|integer|smallint|tinyint)(\(([\d]+)\))?/';
        if (preg_match($intMatch, $type, $matches)) {
            return 'int';
        }

        $floatMatch = '/^(numeric|real|decimal|double|float)(\(([\d]+),([\d]+)\))?/';
        if (preg_match($floatMatch, $type, $matches)) {
            return 'float';
        }

        $stringMatch = '/^(longtext|text|mediumtext|tinytext|varchar|char|enum|set|'
            . 'varbinary|year|date|datetime|time|timestamp)(\(([\d]+)\))?/';
        if (preg_match($stringMatch, $type, $matches)) {
            return 'string';
        }

        return null;
    }

    /**
     * @param string $module
     * @param string $resource
     * @param string $table
     * @return string
     * @throws Exception
     */
    public function createResource(string $module, string $resource, string $table)
    {
        $moduleName = Str::ucfirst($module);
        $resourceName = Str::ucfirst($resource) . 'Resource';
        $tableName = trim($table);

        $resourcePath = base_path("app/Http/Resources/$moduleName");
        $namespace = "App\\Http\\Resources\\$moduleName";

        if (! file_exists($resourcePath)) {
            mkdir($resourcePath, 0775, true);
        }

        [$schema] = (new Scaffold())->getSchema($tableName);

        $dataMapContent = implode(",\n" . str_repeat(' ', 12), array_map(function ($field) {
                return "'$field' => " . '$this->resource->' . $field;
            }, array_diff($schema, array_merge($this->getExcerptColumns(), ['deleted_at'])))) . ',';

        $resourceFile = $resourcePath .'/'. $resourceName . '.php';
        if (! file_exists($resourceFile)) {
            $content = <<<CONTENT
<?php

namespace $namespace;

use App\Models\$moduleName;
use Illuminate\Http\Resources\Json\JsonResource;

class $resourceName extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(\$request) {
        return [
            # [auto-gen-resource]
            $dataMapContent
            # [/auto-gen-resource]
        ];
    }
}
CONTENT;

            // Create resource file
            $file = fopen($resourceFile, 'w');
            fwrite($file, $content);
            fclose($file);

            $ret = 'Resource "\\' . $namespace . '\\' . $resourceName . '" was created!';
        } else {
            throw new Exception("$resourceFile existed, please update manually");

//            $content = file_get_contents($resourceFile);
//            $content = preg_replace_callback_array(
//                [
//                    '~\#\[auto-gen-resource\](.*?)(.|[\r\n\t])*?\[\/auto-gen-resource\]~' => function($match) use($dataMapContent) {
//                        return "# [auto-gen-resource]\n$dataMapContent\n\t\t\t# [/auto-gen-resource]";
//                    }
//                ],
//                $content
//            );
//
//            // Update resource file
//            $file = fopen($resourceFile, 'w');
//            fwrite($file, $content);
//            fclose($file);
//
//            $ret = 'Resource "\\' . $namespace . '\\' . $resourceName . '" was updated!';
        }

        return $ret;
    }
}
