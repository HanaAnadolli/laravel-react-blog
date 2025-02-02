<?php

namespace App\DataTables;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class RoomDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     * @return \Yajra\DataTables\EloquentDataTable
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($query) {
                $editBtn = view('components.edit-button', [
                    'route' => route('rooms.edit', $query->id),
                    'slot' => 'Edit'
                ])->render();

                $deleteBtn = view('components.delete-button', [
                    'route' => route('rooms.destroy', $query->id),
                    'slot' => 'Delete'
                ])->render();

                return '<div class="action-buttons">' . $editBtn . $deleteBtn . '</div>';
            })
            ->editColumn('description', function ($query) {
                return Str::limit($query->description, 50);
            })
            ->editColumn('image', function ($query) {
                if ($query->image) {
                    $imagePath = 'uploads/room_images/' . $query->image;
                    return '<img src="' . asset($imagePath) . '" alt="' . $query->title . '" width="50" height="50">';
                }
                return 'No Image';
            })                
            ->rawColumns(['description', 'image', 'action'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Room $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Room $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('rooms-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('title'),
            Column::make('description')->addClass('align-middle'),
            Column::make('amenities')->addClass('align-middle'),
            Column::make('status')->addClass('align-middle'),
            Column::make('image')->addClass('align-middle'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(200)
                ->addClass('text-center align-middle'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Rooms_' . date('YmdHis');
    }
}
