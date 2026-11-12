# AsistControl Web

Sistema web para la gestión y el control de asistencia académica, desarrollado con Vue.js 3.

## Descripción

AsistControl Web busca facilitar el trabajo del docente mediante una aplicación que permita administrar grupos académicos, gestionar estudiantes y registrar asistencias por fecha y materia.

El proyecto forma parte de la actividad grupal de la asignatura Frameworks con Vue.js 3.

## Objetivo

Desarrollar una aplicación web con una interfaz moderna, responsive y organizada que facilite el registro y la consulta de asistencia académica.

## Tecnologías

- **Vue.js 3:** Framework principal.
- **Vite:** Herramienta de desarrollo y construcción.
- **Vue Router 4:** Navegación entre vistas.
- **Vuex 4:** Administración del estado global.
- **Vuetify 3:** Componentes visuales y diseño responsive.
- **JavaScript:** Lenguaje de programación.
- **localStorage:** Persistencia de información en el navegador.
- **Git y GitHub:** Control de versiones y colaboración.

## Funcionalidades principales

1. Inicio de sesión simulado para docentes.
2. Dashboard principal.
3. Gestión de estudiantes.
4. Gestión de grupos académicos.
5. Registro de asistencia por fecha, grupo y materia.
6. Consulta del historial de asistencia.
7. Cálculo automático del porcentaje de asistencia.
8. Búsqueda y filtrado de registros.
9. Validación de formularios.
10. Notificaciones visuales.
11. Perfil del docente y cierre de sesión.

## Registro e historial de asistencia

En **Asistencia**, selecciona un grupo y usa **Nueva asistencia** para elegir la fecha y asignar a
cada estudiante el estado Presente, Tarde o Ausente. El formulario conserva el grupo de origen y
permite guardar o volver al resumen del grupo. La opción **Historial** muestra los registros por
fecha, materia y grupo; permite filtrar por grupo, buscar y abrir el detalle de estados.

Los registros se almacenan en el `localStorage` del navegador bajo la clave
`asistcontrol-attendances`. Cada registro conserva el identificador y nombre del grupo, la materia,
la fecha y el estado de cada estudiante. Grupo y fecha forman la clave lógica del registro: guardar
otra vez para el mismo grupo y fecha actualiza el registro existente. Los datos se mantienen en el
navegador donde se guardaron y no se sincronizan entre dispositivos.

## Alcance

Esta versión se desarrollará exclusivamente en el frontend.

- No incluye backend.
- No utiliza una base de datos externa.
- No consume APIs.
- Utiliza localStorage para conservar los datos en el navegador.
- El inicio de sesión es simulado y no representa autenticación real.

## Instalación

Clonar el repositorio:

```bash
git clone URL_DEL_REPOSITORIO
```

Ingresar al proyecto:

```bash
cd asistcontrol-web
```

Instalar dependencias:

```bash
npm install
```

Iniciar el servidor:

```bash
npm run dev
```

## Estructura del proyecto

```text
src/
├── assets/
├── components/
├── layouts/
├── plugins/
├── router/
├── services/
├── store/
├── views/
└── App.vue
```

## Trabajo colaborativo

El desarrollo se realizará mediante Git y GitHub.

- Cada integrante trabajará en una rama propia.
- Los cambios se integrarán mediante Pull Requests.
- Se revisará el código antes de incorporarlo a `main`.
- Se mantendrá una estructura organizada y componentes reutilizables.

## Equipo de desarrollo

Proyecto grupal – TDSV221  
Ciclo II-2026 – ITCA-FEPADE.

## Estado

En desarrollo.
