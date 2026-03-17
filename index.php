<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <title>SISTEMA IPS</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
        <span class="navbar-text ml-3"><strong>SISTEMA IPS</strong></span>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <img src="assets/images/HealthC.png" alt="HealthC Logo" class="brand-image img-circle elevation-3" style="opacity: .8; height: 33px; width: 33px; margin-left: 10px;">
            <span class="brand-text font-weight-light">IPS Admin</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">

                    <li class="nav-item" id="nav-pacientes">
                        <a href="#" class="nav-link" onclick="mostrarPacientes()">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Pacientes</p>
                        </a>
                    </li>

                    <li class="nav-item" id="nav-examenes">
                        <a href="#" class="nav-link" onclick="mostrarExamenes()">
                            <i class="nav-icon fas fa-vial"></i>
                            <p>Exámenes Médicos</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content p-4">
            <div class="container" id="contenidoPrincipal"></div>
        </section>
    </div>

</div> <!-- /.wrapper -->

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

<!-- Modal Agregar Examen -->
<div id="modalAgregarExamen" class="modal">
    <div class="modal-content">
        <h3 id="modalExamenTitulo">Agregar Examen</h3>

        <form id="formularioExamen">
            <input type="hidden" id="examenId">

            <label>Código Paciente*</label>
            <input type="number" id="examCode" placeholder="Código del paciente" required>

            <label>Tipo de Examen*</label>
            <select id="examType" required>
                <option value="">Seleccionar tipo de examen</option>
                <option value="Examen de Sangre">Examen de Sangre</option>
                <option value="Radiografía">Radiografía</option>
                <option value="Electrocardiograma">Electrocardiograma</option>
                <option value="Examen de Glucosa">Examen de Glucosa</option>
                <option value="Hemograma">Hemograma</option>
            </select>

            <label>Fecha del Examen*</label>
            <input type="date" id="examDate" required>

            <label>Estado</label>
            <select id="examStatus">
                <option value="pendiente">Pendiente</option>
                <option value="hecho">Hecho</option>
            </select>

            <div class="modal-buttons">
                <button type="button" onclick="cerrarModalExamen()" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-add">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Confirmar Eliminación Examen -->
<div id="modalEliminarExamen" class="modal">
    <div class="modal-content modal-small">
        <h3>¿Eliminar Examen?</h3>
        <p>¿Está seguro de que desea eliminar este examen?</p>
        <div class="modal-buttons">
            <button type="button" onclick="cerrarModalEliminarExamen()" class="btn-cancel">Cancelar</button>
            <button type="button" onclick="confirmarEliminarExamen()" class="btn-delete">Eliminar</button>
        </div>
    </div>
</div>

<script>
let pacientesData = [];
let pacienteAEliminar = null;
let modoEdicion = false;
let examenesData = [];
let examenAEliminar = null;
let modoEdicionExamen = false;

document.addEventListener('DOMContentLoaded', function() {
    mostrarPacientes();
    document.getElementById('formularioPaciente').addEventListener('submit', guardarPaciente);
    document.getElementById('formularioExamen').addEventListener('submit', guardarExamen);
});

function mostrarPacientes() {

    document.getElementById("contenidoPrincipal").innerHTML = `
    <h1>PACIENTES</h1>

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
    `;

    cargarPacientes();

    document.getElementById('searchBox')
    .addEventListener('keyup', filtrarPacientes);

    // Actualizar navegación activa
    document.querySelector('#nav-pacientes .nav-link').classList.add('active');
    document.querySelector('#nav-examenes .nav-link').classList.remove('active');

}

function cargarPacientes() {
    fetch('api/pacientes.php?action=obtener')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pacientesData = data.data;
                renderizarPacientes(pacientesData);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los pacientes');
        });
}

function renderizarPacientes(pacientes) {
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
                <button class="btn-edit" onclick="abrirModalEditar(${paciente.code})" title="Editar">✏️</button>
                <button class="btn-delete" onclick="abrirModalEliminar(${paciente.code})" title="Eliminar">🗑️</button>
            </td>
        </tr>
    `).join('');
}

function renderizarExamenes(examenes) {
    const cuerpo = document.getElementById('cuerpoExamenes');

    if (examenes.length === 0) {
        cuerpo.innerHTML = '<tr><td colspan="6" class="empty-message">No hay exámenes registrados</td></tr>';
        return;
    }

    cuerpo.innerHTML = examenes.map(exam => {
        let estado = exam.status === "pendiente"
        ? "<span class='badge badge-warning'>Pendiente</span>"
        : "<span class='badge badge-success'>Hecho</span>";

        return `
        <tr>
        <td>${exam.idx}</td>
        <td>${exam.code}</td>
        <td>${exam.exam_type}</td>
        <td>${exam.exam_date}</td>
        <td>${estado}</td>
        <td>
            <button class="btn-edit" onclick="abrirModalEditarExamen(${exam.idx})" title="Editar">✏️</button>
            <button class="btn-delete" onclick="abrirModalEliminarExamen(${exam.idx})" title="Eliminar">🗑️</button>
        </td>
        </tr>
        `;
    }).join('');
}

function filtrarPacientes() {
    const termino = document.getElementById('searchBox').value.toLowerCase();

    const filtrados = pacientesData.filter(paciente => 
        paciente.first_name.toLowerCase().includes(termino) ||
        paciente.last_name.toLowerCase().includes(termino) ||
        paciente.code.toString().includes(termino)
    );

    renderizarPacientes(filtrados);
}

function filtrarExamenes() {
    const termino = document.getElementById('searchExamenes').value.toLowerCase();

    const filtrados = examenesData.filter(examen => 
        examen.exam_type.toLowerCase().includes(termino) ||
        examen.code.toString().includes(termino) ||
        examen.idx.toString().includes(termino)
    );

    renderizarExamenes(filtrados);
}

function abrirModal() {
    modoEdicion = false;
    document.getElementById('modalTitulo').textContent = 'Agregar Paciente';
    document.getElementById('pacienteId').value = '';
    document.getElementById('formularioPaciente').reset();
    document.getElementById('modalAgregar').style.display = 'flex';
}

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

function cerrarModal() {
    document.getElementById('modalAgregar').style.display = 'none';
    document.getElementById('formularioPaciente').reset();
}

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

function agregarPaciente(datos) {
    fetch('api/pacientes.php?action=agregar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
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

function editarPaciente(datos) {
    fetch('api/pacientes.php?action=editar', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
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

function abrirModalEliminar(code) {
    pacienteAEliminar = code;
    document.getElementById('modalEliminar').style.display = 'flex';
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminar').style.display = 'none';
    pacienteAEliminar = null;
}

function confirmarEliminar() {
    if (pacienteAEliminar !== null) {
        eliminarPaciente({ code: pacienteAEliminar });
    }
}

function eliminarPaciente(datos) {
    fetch('api/pacientes.php?action=eliminar', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
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

function abrirModalExamen() {
    modoEdicionExamen = false;
    document.getElementById('modalExamenTitulo').textContent = 'Agregar Examen';
    document.getElementById('examenId').value = '';
    document.getElementById('formularioExamen').reset();
    document.getElementById('modalAgregarExamen').style.display = 'flex';
}

function abrirModalEditarExamen(idx) {
    modoEdicionExamen = true;
    document.getElementById('modalExamenTitulo').textContent = 'Editar Examen';

    const examen = examenesData.find(e => e.idx == idx);
    if (examen) {
        document.getElementById('examenId').value = examen.idx;
        document.getElementById('examCode').value = examen.code;
        document.getElementById('examType').value = examen.exam_type;
        document.getElementById('examDate').value = examen.exam_date;
        document.getElementById('examStatus').value = examen.status;
        document.getElementById('modalAgregarExamen').style.display = 'flex';
    }
}

function cerrarModalExamen() {
    document.getElementById('modalAgregarExamen').style.display = 'none';
    document.getElementById('formularioExamen').reset();
}

function guardarExamen(e) {
    e.preventDefault();

    const datos = {
        code: parseInt(document.getElementById('examCode').value),
        exam_type: document.getElementById('examType').value,
        exam_date: document.getElementById('examDate').value,
        status: document.getElementById('examStatus').value
    };

    if (modoEdicionExamen) {
        datos.idx = parseInt(document.getElementById('examenId').value);
        editarExamen(datos);
    } else {
        agregarExamen(datos);
    }
}

function agregarExamen(datos) {
    fetch('api/examenes.php?action=agregar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Examen agregado exitosamente');
            cerrarModalExamen();
            cargarExamenes();
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar examen');
    });
}

function editarExamen(datos) {
    fetch('api/examenes.php?action=editar', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Examen actualizado exitosamente');
            cerrarModalExamen();
            cargarExamenes();
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al editar examen');
    });
}

function abrirModalEliminarExamen(idx) {
    examenAEliminar = idx;
    document.getElementById('modalEliminarExamen').style.display = 'flex';
}

function cerrarModalEliminarExamen() {
    document.getElementById('modalEliminarExamen').style.display = 'none';
    examenAEliminar = null;
}

function confirmarEliminarExamen() {
    if (examenAEliminar !== null) {
        eliminarExamen({ idx: examenAEliminar });
    }
}

function eliminarExamen(datos) {
    fetch('api/examenes.php?action=eliminar', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Examen eliminado exitosamente');
            cerrarModalEliminarExamen();
            cargarExamenes();
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar examen');
    });
}

function mostrarExamenes() {

    document.getElementById("contenidoPrincipal").innerHTML = `
    <h1>EXÁMENES MÉDICOS</h1>

    <div class="top-bar">
        <input 
            type="text" 
            id="searchExamenes"
            class="search-box" 
            placeholder="🔍 Buscar examen..."
        >
        <button class="btn-add" onclick="abrirModalExamen()">+ Agregar</button>
    </div>

    <table id="tablaExamenes" class="table table-bordered table-striped">
    <thead>
    <tr>
    <th>ID</th>
    <th>Código Paciente</th>
    <th>Tipo de Examen</th>
    <th>Fecha</th>
    <th>Estado</th>
    <th>Acciones</th>
    </tr>
    </thead>

    <tbody id="cuerpoExamenes">
    <tr>
    <td colspan="6">Cargando...</td>
    </tr>
    </tbody>
    </table>
    `;

    cargarExamenes();

    document.getElementById('searchExamenes')
    .addEventListener('keyup', filtrarExamenes);

    // Actualizar navegación activa
    document.querySelector('#nav-examenes .nav-link').classList.add('active');
    document.querySelector('#nav-pacientes .nav-link').classList.remove('active');

}

function cargarExamenes(){

    fetch('api/examenes.php?action=obtener')
    .then(response => response.json())
    .then(data => {

    if (data.success) {
        examenesData = data.data;
        renderizarExamenes(examenesData);
    } else {
        document.getElementById("cuerpoExamenes").innerHTML = "<tr><td colspan='5'>Error cargando datos</td></tr>";
    }

    })
    .catch(error=>{
    console.error(error);
    document.getElementById("cuerpoExamenes").innerHTML = "<tr><td colspan='5'>Error de conexión</td></tr>";
    });

}
</script>

<!-- Scripts AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>