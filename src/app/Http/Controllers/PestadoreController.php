<?php

namespace Prestadores\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorDataRequest;
use App\Http\Requests\Utils\ModelController;
use App\Models\Anexo;
use App\Models\ValorProcedimento\ValorProcedimento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Inertia\Inertia;
use JetBrains\PhpStorm\NoReturn;
use Prestadores\App\Module\Prestadores\Prestadores;

class PestadoreController extends Controller
{

    protected $ModelName;
    protected $columns;

    /**
     * Exibe a página inicial do componente.
     */
    public function index(Request $request)
    {
        $screenHeight = $request->header('Screen-Height', 800);
        $itemHeight = 43;


        $itemsPerPage = (int) floor($screenHeight / $itemHeight);

        $prestadores = Prestadores::search($request->search)->orderBy('id', 'desc')
            ->paginate($itemsPerPage)->appends($request->all());
        $prestUid = $request->query('mltUd');
        $anexos = Anexo::where('prestador_uid', $prestUid)->get();

        return Inertia::render('Packages/Prestadores/Components/TableEmpresas', compact('prestadores', 'anexos'));
    }


    public function store(StorDataRequest $request): RedirectResponse
    {
        return $this->extracted($request);
    }

    private function tableName(StorDataRequest $request): void
    {
        $this->ModelName = ModelController::toCamelCase(string: $request->heard_data['content']['tbl_name']);
    }

    /**
     * @param StorDataRequest $request
     * @return RedirectResponse
     */
    public function extracted(StorDataRequest $request): RedirectResponse
    {

        $constData = new Fluent(attributes: $request->heard_data);

        $this->columns = $request->heard_data['content'];
        unset($this->columns['tbl_name']);
        $this->columns['uid'] = $constData->uuid;
        if ($constData->uuid == "null") {

            $this->columns['uid'] = null;
        }

        $namespace = str_replace(search: '.', replace: '\\', subject: $request->heard_data['namespace']);
        $data = app(abstract: $namespace)::createData($this->columns);


        return redirect()->route(
            route: 'Prestadores.index',
            parameters: [
                'key' => \Str::limit(value: encrypt(value: $data), limit: 20),
                'mltUd' => $data[$request->heard_data['uid']]
            ]
        )->with(key: [
                    'success' => 'Post created successfully!',
                    'objectKey' => $data[$request->heard_data['key']],
                    'infoData' => $data[$request->heard_data['uid']],
                ]);
    }

}
