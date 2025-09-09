// Funcionalidades específicas del formulario de citas
document.addEventListener('DOMContentLoaded', function() {
    
    // Solo si estamos en la página de crear citas
    if (!document.getElementById('appointmentForm')) {
        return; // No es la página de crear citas
    }

    // Manejo de selección de pacientes
    function initializePatientSelection() {
        document.querySelectorAll('.patient-row').forEach(function(row) {
            row.addEventListener('click', function() {
                const radio = this.querySelector('.patient-radio');
                const patientId = radio.value;
                radio.checked = true;
                
                // Remover selección anterior
                document.querySelectorAll('.patient-row').forEach(r => r.classList.remove('table-info'));
                // Agregar selección actual
                this.classList.add('table-info');
                
                // Guardar ID del paciente seleccionado
                document.getElementById('selectedPatientId').value = patientId;
                
                // Actualizar información del paciente
                updateSelectedPatientInfo(this);
                
                // Mostrar información del paciente
                showSelectedPatient();
            });
        });

        // También manejar clicks en radio buttons directamente
        document.querySelectorAll('.patient-radio').forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    const patientId = this.value;
                    const row = this.closest('.patient-row');
                    
                    // Remover selección anterior
                    document.querySelectorAll('.patient-row').forEach(r => r.classList.remove('table-info'));
                    // Agregar selección actual
                    row.classList.add('table-info');
                    
                    // Guardar ID del paciente seleccionado
                    document.getElementById('selectedPatientId').value = patientId;
                    
                    // Actualizar información del paciente
                    updateSelectedPatientInfo(row);
                    
                    // Mostrar información del paciente
                    showSelectedPatient();
                }
            });
        });
    }

    // Manejo de especialidades y doctores
    function initializeSpecialtyDoctorCascade() {
        const specialtySelect = document.getElementById('specialty_id');
        const doctorSelect = document.getElementById('doctor_id');
        
        if (!specialtySelect || !doctorSelect) return;

        specialtySelect.addEventListener('change', function() {
            const specialtyId = this.value;
            
            if (specialtyId) {
                fetch('/appointments/get-doctors', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({specialty_id: specialtyId})
                })
                .then(response => response.json())
                .then(doctors => {
                    doctorSelect.innerHTML = '<option value="">Seleccionar doctor...</option>';
                    doctors.forEach(doctor => {
                        doctorSelect.innerHTML += `<option value="${doctor.id}">${doctor.first_name} ${doctor.first_lastname}</option>`;
                    });
                    doctorSelect.disabled = false;
                });
            } else {
                doctorSelect.innerHTML = '<option value="">Primero selecciona una especialidad...</option>';
                doctorSelect.disabled = true;
            }
        });
    }

    // Obtener siguiente slot disponible
    function initializeSlotInfo() {
        const doctorSelect = document.getElementById('doctor_id');
        const nextSlotInfo = document.getElementById('nextSlotInfo');
        const slotDetails = document.getElementById('slotDetails');
        const submitBtn = document.getElementById('submitBtn');
        
        if (!doctorSelect) return;

        doctorSelect.addEventListener('change', function() {
            const doctorId = this.value;
            
            if (doctorId) {
                fetch('/appointments/get-next-slot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({doctor_id: doctorId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        nextSlotInfo.className = 'alert alert-danger';
                        slotDetails.innerHTML = `<p class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>${data.error}</p>`;
                        submitBtn.disabled = true;
                    } else {
                        nextSlotInfo.className = 'alert alert-success';
                        slotDetails.innerHTML = `
                            <p class="mb-1"><strong>Fecha:</strong> ${data.day_name}, ${data.formatted_date}</p>
                            <p class="mb-1"><strong>Hora:</strong> ${data.formatted_time}</p>
                            <p class="mb-1"><strong>Turno:</strong> ${data.slot_number}</p>
                            <p class="mb-0"><strong>Cupos disponibles:</strong> ${data.available_slots}</p>
                        `;
                        submitBtn.disabled = false;
                    }
                    nextSlotInfo.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    nextSlotInfo.className = 'alert alert-danger';
                    slotDetails.innerHTML = '<p class="mb-0">Error al obtener información del cupo disponible.</p>';
                    nextSlotInfo.style.display = 'block';
                    submitBtn.disabled = true;
                });
            } else {
                nextSlotInfo.style.display = 'none';
                submitBtn.disabled = true;
            }
        });
    }

    // Inicializar todas las funcionalidades
    initializePatientSelection();
    initializeSpecialtyDoctorCascade();
    initializeSlotInfo();

    // Inicializar paciente seleccionado si existe
    initializeSelectedPatient();

    // Event listener para el botón de envío
    const submitButton = document.getElementById('submitBtn');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault();
            submitForm();
        });
    }
});

// Funciones globales necesarias para las vistas
function updateSelectedPatientInfo(row) {
    const patientName = row.cells[2].textContent.trim(); // Nombre completo
    const patientCui = row.cells[3].textContent.trim(); // CUI
    const patientAge = row.cells[4].textContent.trim(); // Edad
    
    const patientDetails = document.getElementById('patientDetails');
    patientDetails.innerHTML = `
        <p class="mb-1"><strong>Nombre:</strong> ${patientName}</p>
        <p class="mb-1"><strong>CUI:</strong> ${patientCui}</p>
        <p class="mb-1"><strong>Edad:</strong> ${patientAge}</p>
    `;
}

function showSelectedPatient() {
    document.getElementById('selectedPatientInfo').style.display = 'block';
}

function proceedToStep2() {
    const selectedRadio = document.querySelector('.patient-radio:checked');
    
    if (!selectedRadio) {
        showErrorToast('Por favor, selecciona un paciente antes de continuar.', 'Paciente Requerido');
        return;
    }
    
    const patientId = selectedRadio.value;
    document.getElementById('selectedPatientId').value = patientId;
    
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
}

function backToStep1() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
}

// Inicializar paciente seleccionado si viene como parámetro
function initializeSelectedPatient() {
    const selectedRadio = document.querySelector('.patient-radio:checked');
    if (selectedRadio) {
        const patientId = selectedRadio.value;
        const row = selectedRadio.closest('.patient-row');
        
        document.getElementById('selectedPatientId').value = patientId;
        updateSelectedPatientInfo(row);
        showSelectedPatient();
    }
}

// Función para enviar el formulario
function submitForm() {
    const selectedPatientId = document.getElementById('selectedPatientId').value;
    const specialtyId = document.getElementById('specialty_id').value;
    const doctorId = document.getElementById('doctor_id').value;
    const attentionType = document.querySelector('[name="attention_type"]').value;
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('appointmentForm');
    
    // Validaciones
    if (!selectedPatientId) {
        showErrorToast('Por favor, selecciona un paciente.', 'Paciente Requerido');
        backToStep1();
        return false;
    }
    
    if (!specialtyId) {
        showErrorToast('Por favor, selecciona una especialidad.', 'Especialidad Requerida');
        return false;
    }
    
    if (!doctorId) {
        showErrorToast('Por favor, selecciona un doctor.', 'Doctor Requerido');
        return false;
    }
    
    if (!attentionType) {
        showErrorToast('Por favor, selecciona el tipo de atención.', 'Tipo de Atención Requerido');
        return false;
    }
    
    if (submitBtn.disabled) {
        showWarningToast('Por favor, espera a que se cargue la información del cupo disponible.', 'Esperando Información');
        return false;
    }
    
    // Deshabilitar botón para evitar envíos duplicados
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2 text-white"></i>Procesando...';

    // Enviar formulario
    try {
        form.submit();
    } catch (error) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-calendar-plus me-2"></i>Agendar Cita';
        showErrorToast('Error al enviar el formulario. Por favor, intenta de nuevo.', 'Error del Sistema');
    }
}

// Validar formulario (función de respaldo)
function validateForm() {
    return submitForm();
} 