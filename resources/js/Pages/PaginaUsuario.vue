<template>
    <div class="usuario-page">
      <div class="sidebar">
        <div class="logo-container">
          <div class="logo">Instituto <span class="psi">Psi</span></div>
        </div>
        <div class="user-info">
          <font-awesome-icon class="user-icon" icon="user-circle" />
          <p>Bem-vindo, {{ user }}</p>
        </div>
        <ul>
          <li><a @click.prevent="currentSection = 'historico'" href="#">Histórico de Consultas</a></li>
          <li><a @click.prevent="currentSection = 'novo-agendamento'" href="#">Novo Agendamento</a></li>
          <li><a @click.prevent="currentSection = 'consultas'" href="#">Consultas</a></li>
          <li><a @click.prevent="logout" href="#">Sair</a></li>
        </ul>
      </div>

      <div class="main-content">
        <div v-if="currentSection === 'historico'" class="section">
          <h2>Histórico de Consultas</h2>
          <p>Aqui é possível ver suas sessões antigas!</p>
          <table class="historico-table">
            <thead>
              <tr>
                <th>Profissional</th>
                <th>Data</th>
                <th>Anotações de Sessão</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="historico in consultasPassadas" :key="historico.id">
                <td>{{ historico.medic }}</td>
                <td>{{ historico.time }}</td>
                <td><button @click="viewNotes(historico)">Ver Anotações</button></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="modal">
        <div class="modal-content">
            <span class="close" @click="closeModal()">&times;</span>
            <h2>Informações da Sessão</h2>
            <p><strong>Profissional:</strong> {{ currObservations.professional }}</p>
            <p><strong>Encaminhamentos:</strong> {{ currObservations.referrals_made }}</p>
            <p><strong>Terapêuticas Utilizadas:</strong> {{ currObservations.therapeutics_used }}</p>
        </div>
        </div>


        <div v-if="currentSection === 'novo-agendamento'" class="section">
          <h2>Novo Agendamento</h2>
          <input type="text" v-model="searchQuery" placeholder="Pesquisar profissional" class="search-bar" />
          <ul class="professionals-list">
            <li v-for="professional in professionals" :key="professional.id" class="professional-item">
              <div class="info">
                <p class="name">{{ professional.name }}</p>
              </div>
              <!-- Adicionando manipulador de eventos ao clicar em um profissional -->
              <button @click="showPopup(professional)" class="agendamento-button">Agendamento</button>
            </li>
          </ul>
        </div>

        <div v-if="currentSection === 'consultas'" class="section">
            <h2>Consultas Futuras</h2>
          <p>Aqui é possível ver suas consultas marcadas!</p>
          <table class="historico-table">
            <thead>
              <tr>
                <th>Profissional</th>
                <th>Data & Hora</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="consulta in consultasFuturas" :key="historico.id">
                <td>{{ consulta.medic }}</td>
                <td>{{ consulta.time }}</td>
                <!-- <td><button @click="viewNotes(historico.notes)">Ver Anotações</button></td> -->
                 <!-- um possivel botao de deletar consulta / reagendar -->
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pop-up -->
      <div v-if="popupVisible" class="popup">
        <div class="popup-header">
          <div class="popup-header-left">
            <div class="popup-header-text">
              <h3>{{ selectedProfessional.name }}</h3>
            </div>
          </div>
          <button @click="hidePopup" class="close-button">Fechar</button>
        </div>
        <div class="popup-content">
          <h2>Data de Agendamento</h2>
          <input type="date" class="date-picker" v-model="data_selecionada" @change = "verificarData"/>
          <h3>Horário da Consulta</h3>
          <div class="horarios">
            <button class="horario-button" v-for="horario in horarios" :key="horario" @click="selectTime(horario)">{{ horario }}</button>
          </div>
          <!-- <p v-if="erroData" style="color: red;">{{ erroData }}</p> -->
          <p v-if="horario_selecionado">Horário selecionado: {{ horario_selecionado }}</p>
          <p v-if="erroData" style="color: red;">{{ erroData }}</p>
          <!-- <button class="confirmar-button" @click="confirmAppointment">Confirmar</button> -->
          <button class="confirmar-button" @click="combinarDateTime(data_selecionada)">Confirmar</button>
        </div>
      </div>

    </div>
  </template>

  <script>
  import { ref, computed } from 'vue';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import { library } from '@fortawesome/fontawesome-svg-core';
  import { faUserCircle } from '@fortawesome/free-solid-svg-icons';
  import axios from 'axios';

  library.add(faUserCircle);

  export default {
    props: {user: String,
        userid: [Number,String]
            // psicologos: Object
    },
    mounted() {
      // console.log('User authenticated:',this.user);
    },
    components: {
      FontAwesomeIcon
    },
    methods: {
      logout() {
        this.$inertia.post('/logout');
      },
      async fetchProfessionals() {
        try
        {
            // coleta a lista de psicologos e a atribui para professionals.
          const response = await axios.get('/api/psychologists');
          this.professionals = response.data;
          console.log('oi ',response.data.psicologo);
        //   console.log(this.professionals);
        //   console.log(this.professionals.name)
      } catch (error) {
        console.error(error);
      }
     },
     async fetchPastAppointments() {
        axios.get(`/api/userpastappointments/${this.userid}`)
            .then(response => {
                // console.log(response.data);
                this.consultasPassadas = response.data;
                console.log(this.consultasPassadas);
            })
            .catch(error => {
                console.log('Erro ao buscar consultas:', error);
            });
      },

      async fetchFutureAppointments (){
        axios.get(`/api/userfutureappointments/${this.userid}`)
            .then(response => {
                this.consultasFuturas = response.data;
                console.log(this.consultasFuturas);
            })
            .catch(error => {
                console.log('Erro ao buscar consultas:', error);
            });
      }
    },
    mounted() {
      this.fetchProfessionals();
      this.fetchFutureAppointments();
      this.timer = setInterval(this.fetchFutureAppointments, 5000);
      this.fetchPastAppointments();
    },
    beforeUnmount() {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
    setup(props) {
      const currentSection = ref('historico');
      const horarios = ref(['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00']);
      const searchQuery = ref('');
      const currObservations = ref('');
      const currentPage = ref(1);
      const historicoPage = ref(1);
      const showModal = ref(false);
      const itemsPerPage = 5;
      const historicoItemsPerPage = 5;

      // consulta
      const appointment = ref({
        patient: props.userid,
        medic : 1,
        time: '',
        done: false
      });

      const professionals = ref([]);

      // para combinacao de horario.
      const dateTimeCombinado = ref('');
      const horario_selecionado = ref('');
      const data_selecionada = ref('');
      const erroData = ref('');

      const consultasPassadas = ref([]);
      const consultasFuturas = ref([]);

      const historico = ref([
        { id: 1, professional: 'Ana Silva', date: '2023-01-15', time: '08:00', notes: 'Anotações da sessão 1' },
        { id: 2, professional: 'Bruno Costa', date: '2023-02-10', time: '09:30', notes: 'Anotações da sessão 2' },
        { id: 3, professional: 'Carla Mendes', date: '2023-03-05', time: '11:00', notes: 'Anotações da sessão 3' },
        { id: 4, professional: 'Daniela Rocha', date: '2023-04-12', time: '08:00', notes: 'Anotações da sessão 4' },
        { id: 5, professional: 'Eduardo Santos', date: '2023-05-20', time: '09:30', notes: 'Anotações da sessão 5' },
        { id: 6, professional: 'Fernanda Lima', date: '2023-06-18', time: '11:00', notes: 'Anotações da sessão 6' },
        { id: 7, professional: 'Gustavo Teixeira', date: '2023-07-25', time: '08:00', notes: 'Anotações da sessão 7' },
        { id: 8, professional: 'Helena Alves', date: '2023-08-30', time: '09:30', notes: 'Anotações da sessão 8' },
      ]);

      const filteredProfessionals = computed(() => {
        if (!searchQuery.value) {
          return professionals.value;
        }
        console.log(professionals.value);
        return professionals.value.filter(professional =>
          professional.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
      });
      // TODO: CORRIGIR ISSO
      const totalPages = computed(() => {
        // console.log('debug totalpages:',filteredProfessionals.value);
        return Math.ceil(filteredProfessionals.value.length / itemsPerPage);
      });

      const totalHistoricoPages = computed(() => {
        return Math.ceil(historico.value.length / historicoItemsPerPage);
      });

      const paginatedProfessionals = computed(() => {
        const start = (currentPage.value - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        console.log(filteredProfessionals.value);
        return filteredProfessionals.value.slice(start, end);
      });

      const paginatedHistorico = computed(() => {
        const start = (historicoPage.value - 1) * historicoItemsPerPage;
        const end = start + historicoItemsPerPage;
        return historico.value.slice(start, end);
      });

      const prevPage = () => {
        if (currentPage.value > 1) {
          currentPage.value--;
        }
      };

      const nextPage = () => {
        if (currentPage.value < totalPages.value) {
          currentPage.value++;
        }
      };

      const prevHistoricoPage = () => {
        if (historicoPage.value > 1) {
          historicoPage.value--;
        }
      };

      const nextHistoricoPage = () => {
        if (historicoPage.value < totalHistoricoPages.value) {
          historicoPage.value++;
        }
      };

      const popupVisible = ref(false);
      const selectedProfessional = ref(null);

      const showPopup = (professional) => {
        console.log(professionals.value.length);
        selectedProfessional.value = professional;
        appointment.value.medic = professional.id;
        // console.log(appointment.value);
        // limpa as variaveis de horario, data e a mensagem de erro.
        horario_selecionado.value = "";
        data_selecionada.value = "";
        erroData.value = "";

        // console.log(selectedProfessional.value)
        popupVisible.value = true;
      };

      const hidePopup = () => {
        popupVisible.value = false;
      };

      const viewNotes = (notes) => {
        currObservations.value = notes;
        showModal.value = true;
      };

      const closeModal = () => {
        showModal.value = false;
      };

      const verificarData = () => {
        erroData.value = ''; // Limpa mensagens de erro anteriores
        const dataEscolhida = new Date(data_selecionada.value);
        dataEscolhida.setHours(dataEscolhida.getHours() + 3);
        const hoje = new Date();
        // todo -> resolver o tempo de ate 3 meses para marcar

        // Zera a hora para comparação apenas de data
        hoje.setHours(0, 0, 0, 0);
        dataEscolhida.setHours(0, 0, 0, 0);

        const ano = dataEscolhida.getFullYear();
        const ano_atual = hoje.getFullYear();

        if (ano > 2000) {
            if (dataEscolhida < hoje) {
                erroData.value = 'A data escolhida é inválida.';
            } else if (dataEscolhida >= hoje) {
              if (ano == ano_atual) {
                erroData.value = '';
              } else {
                erroData.value = 'A data escolhida é inválida.';
              }
            } else {
                erroData.value = 'A data escolhida é inválida.';
            }
        } else {
            erroData.value = 'A data escolhida é inválida.';
        }
      };

      const combinarDateTime = async (data) =>{
        // Verifica se a data e o horário foram inseridos
        if (erroData.value) {
            erroData.value = 'A data e horário escolhidos são inválidos.';
        } else if (data && horario_selecionado.value) {
            // Combina a data e o horário
            dateTimeCombinado.value = data + ' ' + horario_selecionado.value;
            console.log(dateTimeCombinado.value);
            console.log("data e horario valido");
            appointment.value.time = dateTimeCombinado.value;
            try {
            const response = await axios.post('/api/appointments', appointment.value);
            console.log('Resposta do servidor: ', response.data);
            popupVisible.value = false;
            alert('Consulta agendada com sucesso.');
            } catch (error) {
                console.error('Erro ao enviar os dados:', error);
            }
        } else {
            console.log("data e horario invalido");
            erroData.value = 'Horário não selecionado ou inválido.';
        }
      };

      // seta o tempo
      const selectTime = (hora) => {
        // appointment.value.date = hora;
        console.log(hora);
        if (erroData.value == 'Horário não selecionado ou inválido.') {
            erroData.value = '';
        }
        horario_selecionado.value = hora;
      };

    //   const fetchPastAppointments = async () => {
    //     axios.get(`/api/userpastappointments/${props.userid}`)
    //         .then(response => {
    //             // console.log(response.data);
    //             consultasPassadas.value = response.data;
    //             console.log(consultasPassadas.value);
    //         })
    //         .catch(error => {
    //             console.log('Erro ao buscar consultas:', error);
    //         });
    //   };

    //   const fetchFutureAppointments = async () => {
    //     axios.get(`/api/userfutureappointments/${props.userid}`)
    //         .then(response => {
    //             consultasFuturas.value = response.data;
    //             console.log(consultasFuturas.value);
    //         })
    //         .catch(error => {
    //             console.log('Erro ao buscar consultas:', error);
    //         });
    //   };

      return {
        consultasPassadas,
        consultasFuturas,
        // fetchPastAppointments,
        // fetchFutureAppointments,
        data_selecionada,
        verificarData,
        erroData,
        dateTimeCombinado,
        horario_selecionado,
        combinarDateTime,
        appointment,
        selectTime,
        currentSection,
        searchQuery,
        currentPage,
        historicoPage,
        itemsPerPage,
        historicoItemsPerPage,
        professionals,
        historico,
        filteredProfessionals,
        totalPages,
        totalHistoricoPages,
        paginatedProfessionals,
        paginatedHistorico,
        prevPage,
        nextPage,
        prevHistoricoPage,
        nextHistoricoPage,
        popupVisible,
        selectedProfessional,
        showPopup,
        hidePopup,
        viewNotes,
        horarios,
        currObservations,
        closeModal,
        showModal
      };
    },
  };
  </script>

  <style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

  body {
    font-family: 'Roboto', sans-serif;
  }

  .usuario-page {
    display: flex;
    min-height: 100vh;
  }

  .sidebar {
    background-color: #474a59;
    color: white;
    padding: 20px;
    width: 250px;
    min-height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    padding-top: 30px;
  }

  .logo-container .logo {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 10px;
  }

  .logo-container .psi {
    color: #89ffdb;
  }

  .user-info {
    text-align: center;
    margin-bottom: 30px;
  }

  .user-info .user-icon {
    font-size: 36px;
    margin-bottom: 10px;
  }

  .user-info p {
    margin: 0;
    font-weight: bold;
  }

  .sidebar ul {
    list-style: none;
    padding: 0;
  }

  .sidebar ul li {
    margin: 20px 0;
  }

  .sidebar ul li a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    display: block;
    padding: 10px 0;
  }

  .sidebar ul li a:hover {
    background-color: #636b6f;
    border-radius: 5px;
    padding-left: 10px;
  }

  .main-content {
    flex-grow: 1;
    padding: 20px;
    margin-left: 250px; /* Espaço para a sidebar */
  }

  .search-bar {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }

  .professionals-list {
    list-style: none;
    padding: 0;
  }

  .professional-item {
    display: flex;
    align-items: center;
    background-color: white;
    padding: 15px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .professional-item .circle {
    width: 50px;
    height: 50px;
    background-color: #89ffdb;
    color: #474a59;
    font-size: 24px;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
  }

  .professional-item .info {
    flex-grow: 1;
  }

  .professional-item .name {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
  }

  .professional-item .profession {
    margin: 0;
    color: #666;
  }

  .professional-item button {
    background-color: #474a59;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }

  .professional-item button:hover {
    background-color: #333640;
  }

  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
  }

  .pagination button {
    background-color: #474a59;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin: 0 10px;
  }

  .pagination button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }

  .popup-header-left {
    display: flex;
    align-items: center;
  }

  .popup-icon {
    width: 50px;
    height: 50px;
    background-color: #89ffdb;
    color: #474a59;
    font-size: 24px;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
  }
  .section {
    margin-bottom: 40px;
  }

  .section h2 {
    font-size: 36px; /* Aumenta o tamanho do título */
    font-weight: bold; /* Deixa o título em negrito */
    color: #474A59; /* Define a cor do título */
  }

  .section p {
    font-size: 18px; /* Aumenta o tamanho do texto abaixo do título */
    color: #474A59; /* Define a cor do texto */
  }

  .historico-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  .historico-table th, .historico-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
  }

  .historico-table th {
    background-color: #f2f2f2;
  }

  .historico-table td button {
    background-color: transparent;
    border: none;
    color: #007bff;
    text-decoration: underline;
    cursor: pointer;
  }

  .historico-table td button:hover {
    color: #0056b3;
  }

  .modal {
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  width: 50%;
}

.close {
  float: right;
  cursor: pointer;
  font-size: 28px;
}

.popup {
  background-color: #ffffff; /* Fundo branco para contraste */
  color: #474a59; /* Texto cinza escuro para leitura fácil */
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 90%;
  max-width: 600px;
  padding: 20px;
  z-index: 1000;
}

.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.popup-header-text h3 {
  margin: 0;
  font-size: 1.5em;
  color: #474a59; /* Mantendo a cor cinza escuro para cabeçalho */
}

.close-button {
  padding: 5px 10px;
  font-size: 0.9em;
  background-color: #474a59; /* Fundo cinza escuro */
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.close-button:hover {
  background-color: #363945; /* Um tom mais escuro de cinza */
}

.popup-content h2,
.popup-content h3 {
  color: #474a59; /* Texto cinza escuro */
  margin: 10px 0;
}

.date-picker {
  width: 100%;
  padding: 8px;
  border-radius: 5px;
  border: 1px solid #ccc;
  margin-bottom: 20px;
}

.horarios {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.horario-button {
  flex: 1 0 30%; /* Ajuste para três botões por linha */
  padding: 10px;
  background-color: #89ffdb; /* Fundo verde água claro */
  color: #474a59; /* Texto cinza escuro */
  border: none;
  border-radius: 5px;
  cursor: pointer;
  text-align: center;
}

.horario-button:hover {
  background-color: #76e4d4; /* Um tom mais escuro do verde água */
}

.confirmar-button {
  padding: 10px 20px;
  width: 100%;
  background-color: #474a59; /* Fundo verde água claro */
  color: #ffffff; /* Texto cinza escuro */
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.confirmar-button:hover {
  background-color: #488179; /* Um tom mais escuro do verde água */
}

  </style>
