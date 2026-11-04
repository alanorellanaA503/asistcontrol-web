// Datos de ejemplo. Cuando haya backend, esta función hará la llamada a la API.
export const groups = [
  {
    id: 1,
    subject: 'Programación I',
    group: 'Grupo 01',
    schedule: 'Lunes y miércoles',
    icon: 'code',
  },
  {
    id: 2,
    subject: 'Base de Datos',
    group: 'Grupo 02',
    schedule: 'Martes y jueves',
    icon: 'database',
  },
  { id: 3, subject: 'Apps Móviles', group: 'Grupo 01', schedule: 'Viernes', icon: 'smartphone' },
]

export function getGroups() {
  return Promise.resolve(groups)
}
