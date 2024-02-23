@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Últimas Noticias Tesla</h2>
        <table id="newsTable" class="table table-bordered">
            <thead>
            <tr>
                <th>Imagen</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Descripción</th>
                <th>Publicado</th>
                <th>Acción</th>
            </tr>
            </thead>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            $('#newsTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('ajax') }}",
                "columns": [
                    {
                        "data": "image",
                        "render": function (data, type, full, meta) {
                            return '<img src="' + data + '" alt="Imagen" style="width:50px; height:auto;"/>';
                        }
                    },
                    {"data": "title"},
                    {
                        "data": "author",
                        "render": function (data, type, full, meta) {
                            return data;
                        }
                    },
                    {"data": "description"},
                    {
                        "data": "publishedAt",
                        "render": function (data, type, row) {
                            return new Date(data).toLocaleDateString();
                        }
                    },
                    {
                        "data": "url",
                        "render": function (data, type, row) {
                            return '<a href="' + data + '" target="_blank">Leer más</a>';
                        }
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                }
            });
        });
    </script>
@endsection
