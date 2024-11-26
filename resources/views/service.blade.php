@extends('layouts.app')

@section('content')
<form id="form" action="{{Route('service')}}" method="post">
    @csrf
    <input type="text" class="d-none" name="json" id="json">
</form>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Crear servicio</h5>
            <div class="input-group">
                <span class="input-group-text">Codigo de Servicio</span>
                <input id="cod" type="text" class="form-control">
            </div>
            <div class="input-group">
                <span class="input-group-text">Nombre</span>
                <input id="name" type="text" class="form-control">
            </div>
            <div class="input-group">
                <span class="input-group-text">Fecha de Inicio</span>
                <input id="date_start" type="datetime-local" class="form-control">
                <span class="input-group-text">Fecha de Finalizacion</span>
                <input id="date_end" type="datetime-local" class="form-control">
            </div>
            <div id="map"></div>
            <div class="input-group">
                <span class="input-group-text">latitud</span>
                <input id="lat" type="number" readonly class="form-control">
                <span class="input-group-text">longitud</span>
                <input id="long" type="number" readonly class="form-control">
            </div>
            <div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Nombre</span>
                    <select class="form-select" id="person-select">
                        @foreach ($persons as $person)
                            <option value="{{ $person->ci }}" data-name="{{ $person->name }}">{{ $person->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary" id="add-person">Añadir</button>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>CI</th>
                            <th>Nombre</th>
                            <th>Encargado</th>
                            <th>Grupo</th>
                        </tr>
                    </thead>
                    <tbody id="person-table">
                        <!-- Las filas se añadirán aquí dinámicamente -->
                    </tbody>
                </table>
                <button type="button" class="btn btn-success mt-3" id="save-service">Guardar Servicio</button>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        #map {
            height: 280px;
        }
    </style>
@endsection

@section('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        var latitude = document.getElementById('lat');
        var longitude = document.getElementById('long');
        var mark = L.marker([0, 0]);
        var map = L.map('map').setView([-17.963828, -67.084078], 13);

        function onMapClick(e) {
            mark.remove();
            mark = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
            latitude.value = e.latlng.lat;
            longitude.value = e.latlng.lng;
        }
        map.on('click', onMapClick);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
    </script>

    <script>
        document.getElementById('add-person').addEventListener('click', function() {
            // Obtener el select y la tabla
            const select = document.getElementById('person-select');
            const tableBody = document.getElementById('person-table');

            // Obtener el CI y el nombre de la persona seleccionada
            const selectedOption = select.options[select.selectedIndex];
            const ci = selectedOption.value;
            const name = selectedOption.getAttribute('data-name');

            // Verificar si la persona ya está en la tabla
            if (document.querySelector(`#person-table tr[data-ci="${ci}"]`)) {
                alert("La persona ya ha sido añadida.");
                return;
            }

            // Crear una nueva fila para la tabla
            const row = document.createElement('tr');
            row.setAttribute('data-ci', ci); // Usar un atributo para evitar duplicados

            row.innerHTML = `
            <td>${ci}</td>
            <td>${name}</td>
            <td><input type="checkbox" class="form-check"></td>
            <td><input type="number" class="form-control"></td>
        `;

            // Añadir la nueva fila al cuerpo de la tabla
            tableBody.appendChild(row);
        });
    </script>
<script>
    document.getElementById('save-service').addEventListener('click', function () {
        // Obtener datos del formulario
        const codigo = document.getElementById('cod').value;
        const servicio = document.getElementById('name').value;
        const fechaInicio = document.getElementById('date_start').value;
        const fechaFin = document.getElementById('date_end').value;
        const latitud = parseFloat(document.getElementById('lat').value) || null;
        const longitud = parseFloat(document.getElementById('long').value) || null;

        // Obtener datos de la tabla
        const tableRows = document.querySelectorAll('#person-table tr');
        const grupos = [];

        tableRows.forEach((row) => {
            const ci = parseInt(row.getAttribute('data-ci')); // CI de la persona
            const grupo = parseInt(row.querySelector('input[type="number"]').value) || null; // Grupo asignado
            const encargado = row.querySelector('input[type="checkbox"]').checked ? ci : null; // Encargado si está marcado

            // Verificar si ya existe un grupo
            let grupoIndex = grupos.findIndex(g => g.grupo === grupo);
            if (grupoIndex === -1 && grupo !== null) {
                // Crear nuevo grupo si no existe
                grupos.push({
                    grupo: grupo,
                    encargado: null,
                    integrantes: []
                });
                grupoIndex = grupos.length - 1;
            }

            // Asignar encargado o añadir integrante
            if (encargado && grupoIndex !== -1) {
                grupos[grupoIndex].encargado = encargado;
            } else if (grupo !== null && grupoIndex !== -1) {
                grupos[grupoIndex].integrantes.push(ci);
            }
        });

        // Construir el objeto final
        const servicioDto = {
            codigo: codigo,
            servicio: servicio,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            latitud: latitud,
            longitud: longitud,
            grupos: grupos
        };

        // Mostrar en consola el JSON generado
        console.log(JSON.stringify(servicioDto, null, 2));
        document.getElementById('json').value = JSON.stringify(servicioDto, null, 2);
        document.getElementById('form').submit();
        alert("Datos recolectados con éxito. Revisa la consola para el JSON.");
    });
</script>
@endsection
