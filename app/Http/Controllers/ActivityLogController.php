<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $logs = Activity::with(['causer.roles'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $logs->getCollection()->transform(function ($log) {
            $userName = 'Sistema';
            if ($log->causer) {
                $user = $log->causer;
                $userName = "{$user->name} - {$user->id}";
            }
            
            return [
                'id' => $log->id,
                'date' => $log->created_at->format('Y-m-d'),
                'time' => $log->created_at->format('H:i:s'),
                'user' => $userName,
                'menu' => $this->extractMenu($log->description),
                'action' => $this->extractAction($log->description),
                'device' => $log->device ?? '-',
                'ip_address' => $log->ip_address ?? '-',
            ];
        });

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $logs,
        ]);
    }

    /**
     * Extract menu name from activity description.
     *
     * @param string $description
     * @return string
     */
    private function extractMenu(string $description): string
    {
        $menuMap = [
            'client' => 'Clientes',
            'supplier' => 'Fornecedores',
            'contact' => 'Contactos',
            'quotation' => 'Propostas',
            'order' => 'Encomendas',
            'supplier-order' => 'Encomendas Fornecedor',
            'supplier-invoice' => 'Faturas Fornecedor',
            'product' => 'Artigos',
            'user' => 'Utilizadores',
            'permission-group' => 'Permissões',
            'country' => 'Países',
            'contact-function' => 'Funções de Contacto',
            'vat-rate' => 'Taxas de IVA',
        ];

        foreach ($menuMap as $key => $menu) {
            if (stripos($description, $key) !== false) {
                return $menu;
            }
        }

        return 'Outro';
    }

    /**
     * Extract action from activity description.
     *
     * @param string $description
     * @return string
     */
    private function extractAction(string $description): string
    {
        if (stripos($description, 'created') !== false || stripos($description, 'criado') !== false) {
            return 'Criar';
        }
        if (stripos($description, 'updated') !== false || stripos($description, 'atualizado') !== false) {
            return 'Editar';
        }
        if (stripos($description, 'deleted') !== false || stripos($description, 'eliminado') !== false) {
            return 'Eliminar';
        }
        if (stripos($description, 'viewed') !== false || stripos($description, 'visualizado') !== false) {
            return 'Visualizar';
        }

        return $description;
    }
}
