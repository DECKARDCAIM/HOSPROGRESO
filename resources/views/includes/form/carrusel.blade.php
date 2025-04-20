<div class="col-lg-7 carousel-side">
    <div id="loginCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active"
                style="background-image: url('{{ asset('img/carrusel/bk1.jpg') }}')">
                <div class="carousel-overlay">
                    <div class="carousel-content">
                        <div class="subtitle">DEDICACIÓN Y SERVICIO</div>
                        <h1>EL CORAZÓN DEL HOSPITAL</h1>
                        <p>Médicos, enfermeros, administrativos, laboratoristas, radiólogos y todo el equipo
                            hospitalario trabajan incansablemente día y noche. Su entrega y sacrificio
                            salvan vidas y dan esperanza a quienes más lo necesitan.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-item"
                style="background-image: url('{{ asset('img/carrusel/bk2.jpeg') }}')">
                <div class="carousel-overlay">
                    <div class="carousel-content">
                        <div class="subtitle">COMPROMISO Y EXCELENCIA</div>
                        <h1>CUIDANDO TU SALUD</h1>
                        <p>Nuestro equipo médico está comprometido con brindar atención de la más alta
                            calidad. Utilizamos tecnología avanzada y tratamientos innovadores para
                            garantizar los mejores resultados para nuestros pacientes.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-item"
                style="background-image: url('{{ asset('img/carrusel/bk3.jpg') }}')">
                <div class="carousel-overlay">
                    <div class="carousel-content">
                        <div class="subtitle">HUMANIDAD Y EMPATÍA</div>
                        <h1>TU BIENESTAR ES NUESTRA PRIORIDAD</h1>
                        <p>Entendemos que cada paciente es único. Nuestro enfoque personalizado combina la
                            experiencia médica con un trato humano y empático, creando un ambiente de
                            confianza y seguridad para todos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('includes.form.footer')

</div>