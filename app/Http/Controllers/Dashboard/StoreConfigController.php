<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EventsLocation;
use App\Models\EventsType;
use App\Models\StoreConfig;
use App\Services\ModelUtilityService;
use Illuminate\Http\Request;

class StoreConfigController extends Controller
{
    public function __construct(protected ModelUtilityService $modelUtilityService) {}

    public function index()
    {
        $configs = [
            'data' => StoreConfig::all(),
            'column' => $this->modelUtilityService->getEditableFields(StoreConfig::class),
        ];

        $eventTypes = [
            'data' => EventsType::all(),
            'column' => $this->modelUtilityService->getEditableFields(EventsType::class),
        ];

        $eventLocations = [
            'data' => EventsLocation::all(),
            'column' => $this->modelUtilityService->getEditableFields(EventsLocation::class),
        ];

        return view('dashboard.config.index', compact('configs', 'eventTypes', 'eventLocations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'value' => [
                'required',
                'string',
            ],
        ]);
        StoreConfig::create($data);

        return redirect()
            ->route('dashboard.config.index')
            ->with('success', 'Config created.');
    }

    public function edit(StoreConfig $config)
    {
        return view('dashboard.config.edit', compact('config'));
    }

    public function update(Request $request, StoreConfig $config)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'value' => [
                'required',
                'string',
            ],
        ]);
        $config->update($data);

        return redirect()
            ->route('dashboard.config.index')
            ->with('success', 'Config updated.');
    }

    public function destroy(StoreConfig $config)
    {
        $config->delete();

        return redirect()
            ->route('dashboard.config.index')
            ->with('success', 'Config deleted.');
    }

    public function storeModel(Request $request)
    {
        $result = $this->modelUtilityService->updateModel($request);
        if ($result['error']) {
            return back()->withErrors($result['errorMsg']);
        }

        return back()->with('success', 'Update Success.');
    }
}
