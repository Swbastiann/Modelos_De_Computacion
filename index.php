<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PACIENTES IPS</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div class="container">
        <h1>PACIENTES IPS</h1>

        <div class="top-bar">
            <input 
                type="text" 
                id="searchBox"
                class="search-box" 
                placeholder="🔍 Buscar usuario..."
            >
            <button class="btn-add" onclick="abrirModal()">+ Agregar</button>
        </div>

        <table id="tablaPacientes">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Estrato</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                <tr><td colspan="6">Cargando...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Modal Agregar/Editar -->
    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <h3 id="modalTitulo">Agregar Paciente</h3>

            <form id="formularioPaciente">
                <input type="hidden" id="pacienteId">

                <label>Nombre*</label>
                <input type="text" id="firstName" placeholder="Nombre completo" required>

                <label>Apellido*</label>
                <input type="text" id="lastName" placeholder="Apellido" required>

                <label>Estrato*</label>
                <input type="number" id="strat" min="1" max="7" required>

                <label>Fecha de nacimiento*</label>
                <input type="date" id="fechaNacimiento" required>

                <div class="modal-buttons">
                    <button type="button" onclick="cerrarModal()" class="btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-add">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div id="modalEliminar" class="modal">
        <div class="modal-content modal-small">
            <h3>¿Eliminar Paciente?</h3>
            <p>¿Está seguro de que desea eliminar este paciente?</p>
            <div class="modal-buttons">
                <button type="button" onclick="cerrarModalEliminar()" class="btn-cancel">Cancelar</button>
                <button type="button" onclick="confirmarEliminar()" class="btn-delete">Eliminar</button>
            </div>
        </div>
    </div>

    <script>
        let pacientesData = [];
        let pacienteAEliminar = null;
        let modoEdicion = false;

            
        // Cargar pacientes al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarPacientes();
            
            // Event listener para búsqueda en tiempo real
            document.getElementById('searchBox').addEventListener('keyup', filtrarPacientes);
            
            // Event listener para el formulario
            document.getElementById('formularioPaciente').addEventListener('submit', guardarPaciente);
        });

        // OBTENER TODOS LOS PACIENTES
        function cargarPacientes() {
            fetch('api/pacientes.php?action=obtener')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        pacientesData = data.data;
                        mostrarPacientes(pacientesData);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los pacientes');
                });
        }

        // MOSTRAR PACIENTES EN LA TABLA
        function mostrarPacientes(pacientes) {
            const cuerpo = document.getElementById('cuerpoTabla');
            
            if (pacientes.length === 0) {
                cuerpo.innerHTML = '<tr><td colspan="6" class="empty-message">No hay pacientes registrados</td></tr>';
                return;
            }

            cuerpo.innerHTML = pacientes.map(paciente => `
                <tr>
                    <td>${paciente.code}</td>
                    <td>${paciente.first_name}</td>
                    <td>${paciente.last_name}</td>
                    <td>${paciente.strat}</td>
                    <td>${paciente.date}</td>
                    <td>
                        <div class='actions'>
                            <button class='btn-edit' onclick="abrirModalEditar(${paciente.code})">✏️</button>
                            <button class='btn-delete' onclick="abrirModalEliminar(${paciente.code})">🗑️</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // FILTRAR PACIENTES POR BÚSQUEDA
        function filtrarPacientes() {
            const termino = document.getElementById('searchBox').value.toLowerCase();
            
            const filtrados = pacientesData.filter(paciente => 
                paciente.first_name.toLowerCase().includes(termino) ||
                paciente.last_name.toLowerCase().includes(termino) ||
                paciente.code.toString().includes(termino)
            );
            
            mostrarPacientes(filtrados);
        }

        // ABRIR MODAL PARA AGREGAR
        function abrirModal() {
            modoEdicion = false;
            document.getElementById('modalTitulo').textContent = 'Agregar Paciente';
            document.getElementById('pacienteId').value = '';
            document.getElementById('formularioPaciente').reset();
            document.getElementById('modalAgregar').style.display = 'flex';
        }

        // ABRIR MODAL PARA EDITAR
        function abrirModalEditar(code) {
            modoEdicion = true;
            document.getElementById('modalTitulo').textContent = 'Editar Paciente';
            
            const paciente = pacientesData.find(p => p.code == code);
            if (paciente) {
                document.getElementById('pacienteId').value = paciente.code;
                document.getElementById('firstName').value = paciente.first_name;
                document.getElementById('lastName').value = paciente.last_name;
                document.getElementById('strat').value = paciente.strat;
                document.getElementById('fechaNacimiento').value = paciente.date;
                document.getElementById('modalAgregar').style.display = 'flex';
            }
        }

        // CERRAR MODAL AGREGAR
        function cerrarModal() {
            document.getElementById('modalAgregar').style.display = 'none';
            document.getElementById('formularioPaciente').reset();
        }

        // GUARDAR PACIENTE
        function guardarPaciente(e) {
            e.preventDefault();

            const datos = {
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                strat: parseInt(document.getElementById('strat').value),
                date: document.getElementById('fechaNacimiento').value
            };

            if (modoEdicion) {
                datos.code = parseInt(document.getElementById('pacienteId').value);
                editarPaciente(datos);
            } else {
                agregarPaciente(datos);
            }
        }

        // AGREGAR PACIENTE
        function agregarPaciente(datos) {
            fetch('api/pacientes.php?action=agregar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Paciente agregado exitosamente');
                    cerrarModal();
                    cargarPacientes();
                } else {
                    alert('❌ Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al agregar paciente');
            });
        }

        // EDITAR PACIENTE
        function editarPaciente(datos) {
            fetch('api/pacientes.php?action=editar', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Paciente actualizado exitosamente');
                    cerrarModal();
                    cargarPacientes();
                } else {
                    alert('❌ Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al editar paciente');
            });
        }

        // ABRIR MODAL ELIMINAR
        function abrirModalEliminar(code) {
            pacienteAEliminar = code;
            document.getElementById('modalEliminar').style.display = 'flex';
        }

        // CERRAR MODAL ELIMINAR
        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').style.display = 'none';
            pacienteAEliminar = null;
        }

        // CONFIRMAR Y ELIMINAR PACIENTE
        function confirmarEliminar() {
            if (pacienteAEliminar !== null) {
                eliminarPaciente({ code: pacienteAEliminar });
            }
        }

        // ELIMINAR PACIENTE
        function eliminarPaciente(datos) {
            fetch('api/pacientes.php?action=eliminar', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Paciente eliminado exitosamente');
                    cerrarModalEliminar();
                    cargarPacientes();
                } else {
                    alert('❌ Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar paciente');
            });
        }
    </script>

</body>
</html>