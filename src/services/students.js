
const STORAGE_KEY = 'asistcontrol-students'

const estudiantesIniciales = [
  { id: 1, groupId: 1, name: 'Ana López', code: 'EST-001' },
  { id: 2, groupId: 1, name: 'Carlos Díaz', code: 'EST-002' },
  { id: 3, groupId: 1, name: 'María Pérez', code: 'EST-003' },
  { id: 4, groupId: 1, name: 'José Hernández', code: 'EST-004' },
  { id: 5, groupId: 1, name: 'Sofía Ramírez', code: 'EST-005' },
  { id: 6, groupId: 2, name: 'Daniel Castro', code: 'EST-006' },
  { id: 7, groupId: 2, name: 'Valeria Gómez', code: 'EST-007' },
  { id: 8, groupId: 2, name: 'Kevin Martínez', code: 'EST-008' },
  { id: 9, groupId: 2, name: 'Andrea Flores', code: 'EST-009' },
  { id: 10, groupId: 3, name: 'Luis Rivera', code: 'EST-010' },
  { id: 11, groupId: 3, name: 'Gabriela Mejía', code: 'EST-011' },
  { id: 12, groupId: 3, name: 'Fernando Alvarado', code: 'EST-012' },
]

function readStudents() {
  const saved = localStorage.getItem(STORAGE_KEY)

  if (saved === null) {
    localStorage.setItem(
      STORAGE_KEY,
      JSON.stringify(estudiantesIniciales)
    )

    return [...estudiantesIniciales]
  }

  const students = JSON.parse(saved)

  if (!Array.isArray(students)) {
    throw new Error('Los estudiantes guardados no son válidos.')
  }

  return students.map((student) => ({
    ...student,
    id: Number(student.id),
    groupId: Number(student.groupId),
  }))
}

function writeStudents(students) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(students))
}

export function getAllStudents() {
  return Promise.resolve(readStudents())
}

export function getStudentsByGroup(groupId) {
  return Promise.resolve(
    readStudents().filter(
      (student) => student.groupId === Number(groupId)
    )
  )
}

export function saveStudent(student) {
  const students = readStudents()

  const name = String(student.name ?? '').trim()
  const code = String(student.code ?? '').trim().toUpperCase()
  const groupId = Number(student.groupId)
  const id = student.id == null ? null : Number(student.id)

  if (!name || !code) {
    throw new Error('Nombre y carné son obligatorios.')
  }

  if (!Number.isInteger(groupId) || groupId <= 0) {
    throw new Error('Selecciona un grupo válido.')
  }

  const duplicate = students.some(
    (s) =>
      String(s.code).toUpperCase() === code &&
      s.id !== id
  )

  if (duplicate) {
    throw new Error('Ya existe un estudiante con ese carné.')
  }

  if (id !== null) {
    const index = students.findIndex((s) => s.id === id)

    if (index === -1) {
      throw new Error('Estudiante no encontrado.')
    }

    students[index] = {
      ...students[index],
      name,
      code,
      groupId,
    }
  } else {
    const nextId = students.reduce(
      (max, s) => Math.max(max, Number(s.id) || 0),
      0
    ) + 1

    students.push({
      id: nextId,
      name,
      code,
      groupId,
    })
  }

  writeStudents(students)

  return Promise.resolve(true)
}

export function deleteStudent(id) {
  const students = readStudents()

  const updated = students.filter(
    (student) => student.id !== Number(id)
  )

  if (updated.length === students.length) {
    throw new Error('Estudiante no encontrado.')
  }

  writeStudents(updated)

  return Promise.resolve(true)
}
