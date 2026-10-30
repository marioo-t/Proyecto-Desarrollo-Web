const iniciar = async () => {
  if (!await iniciarPagina("admin")) return;
  await cargar();
};

const cargar = async () => {
  try {
    const profesores = await api("api/profesores.php");
    $("#filas").innerHTML = profesores.length
      ? profesores.map(fila).join("")
      : '<tr><td colspan="5" class="vacio">No hay profesores.</td></tr>';
  } catch (e) {
    avisar(e.message, "error");
  }
};

const fila = (p) => `<tr>
    <td>${esc(p.nombre)}</td>
    <td>${esc(p.correo)}</td>
    <td>${p.tutorias}</td>
    <td>${Number(p.estado) === 1 ? "Activo" : "Desactivado"}</td>
    <td>${fmtFechaHora(p.fecha_creacion)}</td>
  </tr>`;

$("#formProfesor").addEventListener("submit", async (evento) => {
  evento.preventDefault();
  const error = $("#error");
  const datos = {
    nombre: $("#nombre").value.trim(),
    correo: $("#correo").value.trim(),
    password: $("#password").value,
  };
  error.textContent = "";

  if (datos.nombre.length < 3) {
    error.textContent = "El nombre debe tener al menos 3 caracteres";
    return;
  }
  if (!/^\S+@\S+\.\S+$/.test(datos.correo)) {
    error.textContent = "El correo no es válido";
    return;
  }
  if (datos.password.length < 6) {
    error.textContent = "La contraseña debe tener al menos 6 caracteres";
    return;
  }

  try {
    await accion("api/profesores.php", "POST", datos);
    evento.target.reset();
    cargar();
  } catch (err) {
    error.textContent = err.message;
  }
});

iniciar();
