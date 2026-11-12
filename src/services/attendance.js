// Resumen de asistencia por grupo (datos de ejemplo).
// Cuando haya backend, estas funciones harán la llamada a la API.

const ATTENDANCES_STORAGE_KEY = 'asistcontrol-attendances'
const ATTENDANCE_STATUSES = ['present', 'late', 'absent']

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
  const savedRecords = getAttendances().filter((record) => record.groupId === groupId)

  // Copia la lista base de ejemplo para no modificarla
  const byStudent = new Map(
    summary.students.map((s) => [
      s.id,
      { id: s.id, name: s.name, code: s.code, present: s.present, late: s.late, absent: s.absent },
    ]),
  )

  // Suma cada estado guardado a la cuenta del estudiante
  savedRecords.forEach((record) => {
    record.students.forEach((student) => {
      if (!byStudent.has(student.id)) {
        byStudent.set(student.id, {
          id: student.id,
          name: student.name,
          code: student.code,
          present: 0,
          late: 0,
          absent: 0,
        })
      }
      byStudent.get(student.id)[student.status] += 1
    })
  })

  const students = [...byStudent.values()].map((s) => ({ ...s, percentage: calcPercentage(s) }))
  return Promise.resolve({
    totalClasses: summary.totalClasses + savedRecords.length,
    students,
  })
}

export function getGroupAverage(students) {
  if (!students.length) return 0
  const sum = students.reduce((acc, s) => acc + s.percentage, 0)
  return Math.round((sum / students.length) * 10) / 10
}

export function getAttendances() {
  const savedAttendances = localStorage.getItem(ATTENDANCES_STORAGE_KEY)
  if (savedAttendances === null) return []

  const attendances = JSON.parse(savedAttendances)
  if (!Array.isArray(attendances)) {
    throw new TypeError('Los datos guardados de asistencia no tienen un formato válido.')
  }
  if (
    attendances.some(
      (attendance) =>
        !attendance ||
        attendance.groupId == null ||
        !attendance.group ||
        !attendance.subject ||
        !attendance.date ||
        !Array.isArray(attendance.students) ||
        attendance.students.some(
          (student) => student.id == null || !ATTENDANCE_STATUSES.includes(student.status),
        ),
    )
  ) {
    throw new TypeError('Uno o más registros de asistencia guardados no tienen un formato válido.')
  }

  return attendances
}

export function saveAttendance(attendance) {
  if (!attendance || attendance.groupId == null || !attendance.group || !attendance.subject) {
    throw new TypeError('La asistencia debe incluir un grupo y una materia.')
  }
  if (!attendance.date || !Array.isArray(attendance.students)) {
    throw new TypeError('La asistencia debe incluir una fecha y la lista de estudiantes.')
  }
  if (
    attendance.students.some(
      (student) => student.id == null || !ATTENDANCE_STATUSES.includes(student.status),
    )
  ) {
    throw new TypeError('Cada estudiante debe tener un identificador y un estado válido.')
  }

  const attendances = getAttendances()
  const existingIndex = attendances.findIndex(
    (saved) => saved.groupId === attendance.groupId && saved.date === attendance.date,
  )

  if (existingIndex === -1) {
    attendances.push(attendance)
  } else {
    attendances[existingIndex] = attendance
  }

  localStorage.setItem(ATTENDANCES_STORAGE_KEY, JSON.stringify(attendances))
  return attendance
}
