<?php

namespace Prestadores\App\Module\Prestadores;

use App\Http\API\CodeController;
use App\Models\Trait\HashAuth;
use App\Models\Trait\Search;
use App\Models\Usuarios\PermissionAtents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Fluent;

/**
 * @method static where(string $string, mixed $codigo)
 * @method static paginate(int $int)
 * @method static search(mixed $search)
 */
class Prestadores extends Model
{

    use Search, HashAuth;

    protected $table = 'prestadores';

    protected $fillable = [
        'nome',
        'codigo',
        'tipoPrestador',
        'status',
        'exibirValor',
        'simNaoGuide',
        'coPartipacao',
        'razaoSocial',
        'nif',
        'dataContrato',
        'converter',
        'cotacaoDolar',
        'Observacao',
        'respoRespo',
        'emailRespo',
        'iban',
        'contaCorrente',
        'contactos',
        'tbl_name',
        'address',
        'profile',
        'uid',
        'object_guid'
    ];

    protected $searchable = ['nome', 'nif', 'codigo', 'razaoSocial'];

    public static function createData($data): self
    {
        $attribute = new Fluent($data);

        if ($attribute->uid) {

            $ttr = self::where('uid', $data['uid'])->first();
            $ttr->update($data);
            return $ttr;
        }

        return self::create($data);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {

            if (!$model->uuid) {
                $model->uid = uuid_create();
                $model->codigo = CodeController::generateUniqueCodeUtil('Prestadores');
            }

            $model->status = 1;

            $model->tbl_name = 'prestadores';
        });

        static::retrieved(function ($attr) {

            self::hashAuthentication();

            if (!auth()->user()->super_admin) {
                $permissions = PermissionAtents::where('uid', $attr->uid)
                    ->where('user_id', auth()->id())
                    ->first();

                $attr->aprovaGuia = $permissions ? $permissions->aprovaGuia : false;
                $attr->cancelaGuia = $permissions ? $permissions->cancelaGuia : false;
            }

            if(auth()->user()->super_admin){
                $attr->aprovaGuia = true;
                $attr->cancelaGuia = true;
            }
        });


    }
}
