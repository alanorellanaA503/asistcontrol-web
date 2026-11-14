
import { getStudentsByGroup } from '../store/modules/students'

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

export function calcPercentage({ present, late, absent }) {
  const total = present + late + absent

  if (total === 0) return 0

  return Math.round(
    ((present + late * 0.5) / total) * 1000
  ) / 10
}

export async function getGroupAttendance(groupId) {
  const id = Number(groupId)

  const currentStudents = await getStudentsByGroup(id)

  const summary = attendanceSummary[id] ?? {
    totalClasses: 0,
    students: [],
  }

  const savedRecords = getAttendances().filter(
    (record) => Number(record.groupId) === id
  )

  const students = currentStudents.map((student) => {
    const initial = summary.students.find(
      (s) => s.id === student.id
    )

    const result = {
      id: student.id,
      name: student.name,
      code: student.code,
      present: initial?.present ?? 0,
      late: initial?.late ?? 0,
      absent: initial?.absent ?? 0,
    }

    for (const record of savedRecords) {
      const saved = record.students.find(
        (s) => Number(s.id) === student.id
      )

      if (saved && ATTENDANCE_STATUSES.includes(saved.status)) {
        result[saved.status] += 1
      }
    }

    return {
      ...result,
      percentage: calcPercentage(result),
    }
  })

  return {
    totalClasses: summary.totalClasses + savedRecords.length,
    students,
  }
}

export function getGroupAverage(students) {
  if (!students.length) return 0

  const sum = students.reduce(
    (acc, student) => acc + student.percentage, 0
  )

  return Math.round((sum / students.length) * 10) / 10
}

export function getAttendances() {
  const saved = localStorage.getItem(ATTENDANCES_STORAGE_KEY)

  if (saved === null) return []

  const attendances = JSON.parse(saved)

  if (!Array.isArray(attendances)) {
    throw new TypeError(
      'Los datos de asistencia no tienen un formato válido.'
    )
  }

  const invalid = attendances.some(
    (attendance) =>
      !attendance ||
      attendance.groupId == null ||
      !attendance.group ||
      !attendance.subject ||
      !attendance.date ||
      !Array.isArray(attendance.students) ||
      attendance.students.some(
        (student) =>
          student.id == null ||
          !ATTENDANCE_STATUSES.includes(student.status)
      )
  )

  if (invalid) {
    throw new TypeError(
      'Uno o más registros de asistencia no son válidos.'
    )
  }

  return attendances
}

export function saveAttendance(attendance) {
  const groupId = Number(attendance?.groupId)

  if (
    !attendance ||
    !Number.isInteger(groupId) ||
    groupId <= 0 ||
    !attendance.group ||
    !attendance.subject
  ) {
    throw new TypeError(
      'La asistencia debe incluir grupo y materia.'
    )
  }

  if (!attendance.date || !Array.isArray(attendance.students)) {
    throw new TypeError(
      'La asistencia necesita una fecha y estudiantes.'
    )
  }

  const invalid = attendance.students.some(
    (student) =>
      student.id == null ||
      !ATTENDANCE_STATUSES.includes(student.status)
  )

  if (invalid) {
    throw new TypeError(
      'Cada estudiante necesita un identificador y estado válido.'
    )
  }

  const attendances = getAttendances()

  const newAttendance = {
    ...attendance,
    groupId,
  }

  const existingIndex = attendances.findIndex(
    (saved) =>
      Number(saved.groupId) === groupId &&
      saved.date === attendance.date
  )

  if (existingIndex === -1) {
    attendances.push(newAttendance)
  } else {
    attendances[existingIndex] = newAttendance
  }

  localStorage.setItem(
    ATTENDANCES_STORAGE_KEY,
    JSON.stringify(attendances)
  )

  return newAttendance
}
