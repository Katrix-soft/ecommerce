<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\Driver;
use App\Models\Product;
use App\Models\Variant;
use App\Models\User;
use App\Models\TenantMetric;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('superadmin');
        
        if ($isSuperAdmin) {
            $enabledMetrics = [];
            foreach (config('dashboard_metrics', []) as $category) {
                foreach ($category['items'] as $key => $item) {
                    $enabledMetrics[] = $key;
                }
            }
        } else {
            $enabledMetrics = TenantMetric::where('user_id', $user->id)
                ->where('is_enabled', true)
                ->pluck('metric_key')
                ->toArray();
        }

        $data = [];

        // 1. VENTAS
        if (in_array('ingresos_totales', $enabledMetrics)) {
            $data['ingresos_totales'] = Order::where('status', '!=', 'cancelled')->sum('total');
        }
        if (in_array('ticket_promedio', $enabledMetrics)) {
            $data['ticket_promedio'] = Order::where('status', '!=', 'cancelled')->avg('total') ?: 0;
        }
        if (in_array('ventas_vs_mes_anterior', $enabledMetrics)) {
            $thisMonth = Order::where('status', '!=', 'cancelled')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total');
            $lastMonth = Order::where('status', '!=', 'cancelled')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('total');
            
            $diff = $thisMonth - $lastMonth;
            $percentage = $lastMonth > 0 ? ($diff / $lastMonth) * 100 : ($thisMonth > 0 ? 100 : 0);
            $data['ventas_vs_mes_anterior'] = [
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
                'percentage' => round($percentage, 1),
                'direction' => $diff >= 0 ? 'up' : 'down'
            ];
        }
        if (in_array('grafico_ventas', $enabledMetrics)) {
            // Last 7 days sales
            $salesData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $sum = Order::where('status', '!=', 'cancelled')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total');
                $salesData[] = [
                    'label' => $date->isoFormat('ddd'),
                    'value' => $sum,
                    'date' => $date->format('d/m')
                ];
            }
            $maxVal = collect($salesData)->max('value') ?: 1;
            foreach ($salesData as &$item) {
                $item['percentage'] = round(($item['value'] / $maxVal) * 100);
            }
            $data['grafico_ventas'] = $salesData;
        }

        // 2. ÓRDENES
        if (in_array('ordenes_del_dia', $enabledMetrics)) {
            $data['ordenes_del_dia'] = Order::whereDate('created_at', today())->count();
        }
        if (in_array('ordenes_pendientes', $enabledMetrics)) {
            $data['ordenes_pendientes'] = Order::whereIn('status', ['pending', 'processing'])->count();
        }
        if (in_array('ordenes_canceladas', $enabledMetrics)) {
            $data['ordenes_canceladas'] = Order::where('status', 'cancelled')->count();
        }

        // 3. LOGÍSTICA
        if (in_array('envios_activos', $enabledMetrics)) {
            $data['envios_activos'] = Shipment::where('status', 'in_transit')->count();
        }
        if (in_array('tiempo_entrega_prom', $enabledMetrics)) {
            $shipments = Shipment::where('status', 'delivered')
                ->whereNotNull('shipped_at')
                ->whereNotNull('delivered_at')
                ->get();
            
            $totalHours = 0;
            $count = $shipments->count();
            foreach ($shipments as $shipment) {
                $shipped = \Carbon\Carbon::parse($shipment->shipped_at);
                $delivered = \Carbon\Carbon::parse($shipment->delivered_at);
                $totalHours += $shipped->diffInHours($delivered);
            }
            $avgHours = $count > 0 ? $totalHours / $count : 0;
            if ($avgHours >= 24) {
                $data['tiempo_entrega_prom'] = round($avgHours / 24, 1) . ' días';
            } else {
                $data['tiempo_entrega_prom'] = round($avgHours, 1) . ' horas';
            }
        }
        if (in_array('conductores_activos', $enabledMetrics)) {
            $data['conductores_activos'] = Driver::where('is_active', true)->count();
        }

        // 4. PRODUCTOS
        if (in_array('mas_vendidos', $enabledMetrics)) {
            $data['mas_vendidos'] = OrderItem::select('variant_id', DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('variant_id')
                ->orderBy('total_qty', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $variant = Variant::with(['product', 'features'])->find($item->variant_id);
                    return [
                        'product_name' => $variant ? $variant->product->name : 'Producto',
                        'features' => $variant ? $variant->features->map(function($f) {
                            return [
                                'value' => $f->value,
                                'description' => $f->description,
                            ];
                        })->toArray() : [],
                        'quantity' => $item->total_qty,
                    ];
                })->toArray();
        }
        if (in_array('stock_bajo', $enabledMetrics)) {
            $data['stock_bajo'] = Variant::with(['product', 'features'])
                ->where('stock', '<=', 5)
                ->get()
                ->map(function ($variant) {
                    return [
                        'product_name' => $variant->product->name,
                        'features' => $variant->features->map(function($f) {
                            return [
                                'value' => $f->value,
                                'description' => $f->description,
                            ];
                        })->toArray(),
                        'stock' => $variant->stock,
                    ];
                })->toArray();
        }
        if (in_array('mas_visitados', $enabledMetrics)) {
            $data['mas_visitados'] = Product::limit(3)->get()->map(function ($product, $index) {
                return [
                    'name' => $product->name,
                    'visits' => [142, 98, 74][$index] ?? 45,
                ];
            })->toArray();
        }

        // 5. USUARIOS
        if (in_array('nuevos_registros', $enabledMetrics)) {
            $data['nuevos_registros'] = User::where('created_at', '>=', now()->subDays(30))->count();
        }
        if (in_array('clientes_recurrentes', $enabledMetrics)) {
            $data['clientes_recurrentes'] = Order::where('status', '!=', 'cancelled')
                ->select('user_id', DB::raw('count(*) as count'))
                ->groupBy('user_id')
                ->having('count', '>', 1)
                ->get()
                ->count();
        }

        return view('livewire.admin.dashboard', [
            'enabledMetrics' => $enabledMetrics,
            'data' => $data,
        ]);
    }
}
