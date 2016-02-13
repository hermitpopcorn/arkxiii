@extends('skeleton')

@section('title', 'Ganti Semester Aktif')

@section('content')
<div class='navbuttons row'>
    <a class='button primary' href="{{ route('semester') }}"><i class='fa fa-calendar'></i> Majukan Semester</a>
</div>

<div class='row'><h2>Ganti Semester Aktif</h2></div>

<div class='panel warning row'>
    <span class='alert-icon'><i class='alert-icon fa fa-warning'></i></span> Harap berhati-hati dalam melakukan proses pergantian semester, karena kebanyakan data di database bergantung pada semester yang sedang aktif.
</div>

@include('page_alerts')

<div class='row'>
    <div class='large-12 column'>
        <table class='expand' id="datatable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Semester</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semester as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>Semeser <b>{{ $s->semester }}</b> tahun ajaran <b>{{ $s->tahun_ajaran }}</b></td>
                    @if($s->aktif == 0)
                    <td><center><a href="javascript:pilih({{ $s->id }})">Aktifkan</a></center></td>
                    @else
                    <td><center><b>Aktif</b></center></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<form method="POST" action="{{ route('semester.ganti.action') }}" id='ganti'>
    <input type='hidden' name='_token' value='{{ csrf_token() }}' />
    <input type='hidden' name='id' value='' />
</form>

<script type='text/javascript'>
function pilih(id) {
    $('form#ganti input[name=id]').val(id);
    $('form').submit();
}
</script>
@endsection