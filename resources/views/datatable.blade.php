<!-- Datatable start -->
<table class='expand' id="datatable">
    <thead>
        <tr>
            @foreach ($columns as $column)
            @if ($column == "No" || $column == "ID")
            <th style='text-align:center'>{{$column}}</th>
            @else
            <th>{{$column}}</th>
            @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr><td colspan='99'><center><i class='fa fa-spin fa-refresh'></i></center></td></tr>
    </tbody>
</table>
<!-- Datatable stop -->

<!-- Pagination start -->
<center>
    <ul class='pagination'>
        <li class='unavailable'><a>Sedang dalam proses...</a></i></li>
    </ul>
</center>
<!-- Pagination end -->
