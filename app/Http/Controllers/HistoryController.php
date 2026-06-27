<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $checkins = CheckIn::where('user_id', $user->id)
            ->with('template', 'items.templateItem')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(fn($c) => $c->date->format('F Y'));

        return view('history.index', ['grouped' => $checkins]);
    }

    public function show(CheckIn $checkIn)
    {
        if ($checkIn->user_id !== Auth::id()) {
            abort(403);
        }

        $checkIn->load('template', 'items.templateItem');

        return view('history.show', ['checkin' => $checkIn]);
    }

    public function trends()
    {
        $user = Auth::user();

        $checkins = CheckIn::where('user_id', $user->id)
            ->with('items.templateItem')
            ->orderBy('date', 'asc')
            ->get();

        // Organize data per template item (dimension)
        $dimensions = [];

        foreach ($checkins as $checkin) {
            foreach ($checkin->items as $item) {
                $label = $item->templateItem->label ?? '?';
                $type = $item->templateItem->input_type ?? 'text';
                $date = $checkin->date->toDateString();

                if (!isset($dimensions[$label])) {
                    $dimensions[$label] = [
                        'type' => $type,
                        'data' => [],
                    ];
                }

                $value = $item->value;
                if ($type === 'slider') {
                    $value = (int) $value;
                } elseif ($type === 'emoji') {
                    $map = ['😢'=>1,'😟'=>2,'😐'=>3,'🙂'=>4,'😊'=>5,'😄'=>6,'😁'=>7,'🥳'=>8];
                    $value = $map[$value] ?? null;
                } elseif ($type === 'checkbox') {
                    $decoded = json_decode($value, true);
                    $value = is_array($decoded) ? implode(', ', $decoded) : $value;
                }

                $dimensions[$label]['data'][] = [
                    'date' => $date,
                    'value' => $value,
                    'raw' => $item->value,
                ];
            }
        }

        return view('history.trends', ['dimensions' => $dimensions]);
    }
}
