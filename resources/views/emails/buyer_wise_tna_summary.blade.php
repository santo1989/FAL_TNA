<!-- resources/views/emails/buyer_wise_tna_summary.blade.php -->
<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white; overflow-x: auto;">
        <div class="container-fluid pt-2">
            <h4 class="text-center">Buyer-Wise Pending Tasks Summary</h4>
            <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
                <thead class="thead-dark">
                    <tr>
                        <th>Buyer</th>
                        @foreach ($columns as $column)
                            <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($buyers as $buyer => $data)
                        <tr>
                            <td>{{ $buyer }}</td>
                            @foreach ($columns as $column)
                                <td>
                                    @if ($data['data'][$column] > 0)
                                        {{ $data['data'][$column] }}
                                    @else
                                        {{ $data['data'][$column] }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-backend.layouts.master>
