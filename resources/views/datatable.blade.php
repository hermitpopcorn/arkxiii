<!-- Datatable start -->
<table class='expand' id="datatable">
    <thead>
        <tr>
            @foreach ($columns as $column)
            <th>{{$column}}</th>
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