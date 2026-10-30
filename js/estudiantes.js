let estudiantes = [];

const iniciar = async () => {
  if (!await iniciarPagina("profesor")) return;
  try {
    estudiantes = await api("api/estudiantes.php");
    llenarFiltro();
    pintar();
  } catch (e) {
    avisar(e.message, "error");
  }
};

const llenarFiltro = () => {
  const tutorias = new Map(estudiantes.map((e) => [e.tutoria_id, e.titulo]));
  $("#f_tutoria").innerHTML += [...tutorias].map(([id, titulo]) =>
    `<option value="${id}">${esc(titulo)}</option>`).join("");
};

const pintar = () => {
  const filtro = $("#f_tutoria").value;
  const lista = estudiantes.filter((e) => !filtro || String(e.tutoria_id) === filtro);
  $("#filas").innerHTML = lista.length
    ? lista.map(fila).join("")
    : '<tr><td colspan="6" class="vacio">No hay estudiantes inscritos.</td></tr>';
};

const fila = (e) => `<tr>
    <td>${esc(e.nombre)}</td>
    <td>${esc(e.correo)}</td>
    <td><a href="tutoria.php?id=${e.tutoria_id}">${esc(e.titulo)}</a> ${e.estado_tutoria === "cancelada" ? insignia("cancelada") : ""}</td>
    <td>${esc(e.materia)} / ${esc(e.tema)}</td>
    <td>${fmtFecha(e.fecha)} ${fmtHora(e.hora_inicio)}</td>
    <td>${fmtFechaHora(e.fecha_inscripcion)}</td>
  </tr>`;

$("#f_tutoria").addEventListener("change", pintar);

iniciar();
