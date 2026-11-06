// Resumen de asistencia por grupo (datos de ejemplo).
// Cuando haya backend, estas funciones harán la llamada a la API.

export const attendanceSummary = {
  1: {
    totalClasses: 20,
    students: [
      { id: 1, name: 'Ana López', code: 'EST-001', present: 19, late: 1, absent: 0 },
      { id: 2, name: 'Carlos Díaz', code: 'EST-002', present: 16, late: 2, absent: 2 },
      { id: 3, name: 'María Pérez', code: 'EST-003', present: 18, late: 0, absent: 2 },
      { id: 4, name: 'José Hernández', code: 'EST-004', present: 12, late: 2, absent: 6 },
      { id: 5, name: 'Sofía Ramírez', code: 'EST-005', present: 20, late: 0, absent: 0 },
    ],
  },
  2: {
    totalClasses: 18,
    students: [
      { id: 6, name: 'Daniel Castro', code: 'EST-006', present: 17, late: 1, absent: 0 },
      { id: 7, name: 'Valeria Gómez', code: 'EST-007', present: 14, late: 2, absent: 2 },
      { id: 8, name: 'Kevin Martínez', code: 'EST-008', present: 11, late: 1, absent: 6 },
      { id: 9, name: 'Andrea Flores', code: 'EST-009', present: 18, late: 0, absent: 0 },
    ],
  },
  3: {
    totalClasses: 10,
    students: [
      { id: 10, name: 'Luis Rivera', code: 'EST-010', present: 9, late: 1, absent: 0 },
      { id: 11, name: 'Gabriela Mejía', code: 'EST-011', present: 8, late: 0, absent: 2 },
      { id: 12, name: 'Fernando Alvarado', code: 'EST-012', present: 6, late: 2, absent: 2 },
    ],
  },
}

// La tarde cuenta como media falta: suma 0.5 en lugar de 1
export function calcPercentage({ present, late, absent }) {
  const total = present + late + absent
  if (total === 0) return 0
  return Math.round(((present + late * 0.5) / total) * 1000) / 10
}

export function getGroupAttendance(groupId) {
  const summary = attendanceSummary[groupId] ?? { totalClasses: 0, students: [] }
  const students = summary.students.map((s) => ({ ...s, percentage: calcPercentage(s) }))
  return Promise.resolve({ totalClasses: summary.totalClasses, students })
}

export function getGroupAverage(students) {
  if (!students.length) return 0
  const sum = students.reduce((acc, s) => acc + s.percentage, 0)
  return Math.round((sum / students.length) * 10) / 10
}