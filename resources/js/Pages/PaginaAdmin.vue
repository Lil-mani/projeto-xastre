<template>
    <div class="new-page">
      <div :class="['sidebar', { hidden: isSidebarHidden }]">
        <div class="logo-container">
          <div class="logo">Instituto <span class="psi">Psi</span></div>
        </div>
        <div class="user-info">
          <font-awesome-icon class="user-icon" icon="user-circle" />
          <p>Bem-vindo, {{ user }}</p>
        </div>
        <nav class="nav-menu">
          <ul>
            <li @click="selectSection('Novos Contatos')" :class="{ active: selectedSection === 'Novos Contatos' }">Registrar Funcionários</li>
            <li @click="selectSection('Lista de Usuários')" :class="{ active: selectedSection === 'Lista de Usuários' }">Lista de Usuários</li>
            <li @click="logout">Sair</li>
          </ul>
        </nav>
      </div>

      <div class="main-content">
        <button @click="toggleSidebar" class="toggle-button">
          <font-awesome-icon :icon="[isSidebarHidden ? 'arrow-right' : 'arrow-left']" />
        </button>

        <div v-if="selectedSection === 'Novos Contatos'">
            <h2 class="section-title">Registrar Funcionários</h2>
                <ul>
                    <form @submit.prevent="submit">
                        <div class="form-row">
                        <label for="name">Nome:</label>
                        <input type="text" id="name" v-model="form.name" required>
                        </div>
                        <div class="form-row">
                        <label for="email">Email:</label>
                        <input type="email" id="email" v-model="form.email" required>
                        </div>
                        <div class="form-row">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" v-model="form.password" required>
                        </div>
                        <div class="form-row">
                        <label for="confirm-password">Confirmar Senha:</label>
                        <input type="password" id="confirm-password" v-model="form.password_confirmation" required>
                        </div>
                        <div class="form-row">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" v-model="form.cpf">
                        </div>
                        <div class="form-row">
                        <label for="telefone">Telefone:</label>
                        <input type="text" id="telefone" v-model="form.telefone">
                        </div>
                        <div class="form-row">
                        <label for="dob">Data de Nascimento:</label>
                        <input type="date" id="dob" v-model="form.dob">
                        </div>
                        <div class="form-row">
                        <label for="cep">CEP:</label>
                        <input type="text" id="cep" v-model="form.cep" @blur="fetchAddress">
                        </div>
                        <p v-if="erroCep" style="color: red;">{{ erroCep }}</p>
                        <div class="form-row">
                        <label for="logradouro">Logradouro:</label>
                        <input type="text" id="logradouro" v-model="form.logradouro">
                        </div>
                        <div class="form-row">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" v-model="form.complemento">
                        </div>
                        <div class="form-row">
                        <label for="bairro">Bairro:</label>
                        <input type="text" id="bairro" v-model="form.bairro">
                        </div>
                        <div class="form-row">
                        <label for="localidade">Cidade:</label>
                        <input type="text" id="localidade" v-model="form.localidade">
                        </div>
                        <div class="form-row">
                        <label for="uf">UF:</label>
                        <input type="text" id="uf" v-model="form.uf">
                        </div>
                        <div class="form-row">
                        <label for="role">Função:</label>
                        <select id="role" v-model="form.role">
                            <option value="secretaria">Secretária</option>
                            <option value="psicologo">Psicólogo</option>
                        </select>
                        </div>
                        <div class="form-row">
                        <button type="submit">Registrar</button>
                        <button type="button" @click="modalVisible = false">Cancelar</button>
                        </div>
                    </form>
                </ul>
        </div>
        <div v-if="selectedSection === 'Lista de Usuários'">
        <h2 class="section-title">Usuários Cadastrados</h2>
        <div class="users-list">
          <table>
            <thead>
              <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Função</th>
                <!-- <th>Ações</th> -->
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.role }}</td>
                <!-- <td>
                  <button @click="editUser(user)">Editar</button>
                  <button @click="deleteUser(user.id)">Excluir</button>
                </td> -->
              </tr>
            </tbody>
          </table>
        </div>
      </div>
        <!-- Modal -->
    <div v-if="modalVisible" class="modal">
      <form @submit.prevent="submit">
        <div class="form-row">
            <input type="text" v-model="form.name" placeholder="Nome" />
            <input type="text" v-model="form.surname" placeholder="Sobrenome" />
            </div>
            <input type="email" v-model="form.email" placeholder="Email" />
            <input type="password" v-model="form.password" placeholder="Senha" />
            <input type="password" v-model="form.password_confirmation" placeholder="Confirmar Senha" />
            <input type="text" v-model="form.cpf" placeholder="CPF" />
            <input type="text" v-model="form.telefone" placeholder="Telefone" />
            <input type="date" v-model="form.dob" placeholder="Data de Nascimento" />
            <input type="text" v-model="form.cep" placeholder="CEP" @blur="fetchAddress" />
            <input type="text" v-model="form.logradouro" placeholder="Logradouro" />
            <input type="text" v-model="form.complemento" placeholder="Complemento" />
            <input type="text" v-model="form.bairro" placeholder="Bairro" />
            <input type="text" v-model="form.localidade" placeholder="Localidade" />
            <input type="text" v-model="form.uf" placeholder="UF" />
        <div class="flex items-center justify-end mt-4">
          <button type="button" @click="modalVisible = false">Close</button>
          <!-- <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Register
          </button> -->
          <button type="submit">Register</button>
        </div>
      </form>
    </div>
        </div>
      </div>
  </template>

  <script>
  import { ref } from 'vue';
  import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
  import { library } from '@fortawesome/fontawesome-svg-core';
  import { Head, Link, useForm } from '@inertiajs/vue3';
  import { faUserCircle, faArrowLeft, faArrowRight } from '@fortawesome/free-solid-svg-icons';
  import DocumentEditor from '../Components/DocumentEditor.vue';
import axios from 'axios';


  library.add(faUserCircle, faArrowLeft, faArrowRight);

  export default {
    props: {
        user: String
    },
    // data() {
    //   return {
    //     form: {
    //       name: '',
    //       email: '',
    //       password: '',
    //       password_confirmation: '',
    //       cpf: '',
    //       telefone: '',
    //       dob: '',
    //       cep: '',
    //       logradouro: '',
    //       complemento: '',
    //       bairro: '',
    //       localidade: '',
    //       uf: '',
    //       role: 'usuario'
    //     }
    //   }
    // },
    methods:{
        logout() {
            this.$inertia.post('/logout');
        },
        showModal(contact) {
            this.form.email = contact.email; // Pré-popular o campo de e-mail, se desejado
            this.form.name = contact.name;
            this.form.surname = contact.surname;
            this.contact = contact.id;
            this.modalVisible = true;
        },
    },
    components: {
      FontAwesomeIcon,
      DocumentEditor
    },
    setup() {
      const isSidebarHidden = ref(false);
      const selectedSection = ref('Novos Contatos');
      const contacts = ref([]);
      const contact = ref(0);
      const modalVisible = ref(false);
      const erroCep = ref('');
      const users = ref([]);
      const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        cpf: '',
        telefone: '',
        dob: '',
        cep: '',
        logradouro: '',
        complemento: '',
        bairro: '',
        localidade: '',
        uf: '',
        role: 'usuario'
        });

      const toggleSidebar = () => {
        isSidebarHidden.value = !isSidebarHidden.value;
      };


      const selectSection = (section) => {
        selectedSection.value = section;
        fetchUsers();
      };
      // api de cep
      const fetchAddress = async () => {
        erroCep.value = ''; 
        if (form.cep.length === 8) {
            try {
                const response = await axios.get(`https://viacep.com.br/ws/${form.cep}/json/`);
                if (!response.data.erro) {
                    form.logradouro = response.data.logradouro;
                    form.complemento = response.data.complemento;
                    form.bairro = response.data.bairro;
                    form.localidade = response.data.localidade;
                    form.uf = response.data.uf;
                } else {
                    erroCep.value = 'CEP não encontrado.';
                }
            } catch (error) {
                console.error('Erro ao buscar o CEP:', error);
                alert('Erro ao buscar o CEP.');
            }
        }
    };
      const fetchUsers = async () => {
          const response = await axios.get('/api/usuarios')
              .then(response => {
                users.value = response.data;
                console.log(response.data);
              });
      };

    const resetForm = () => {
        form.value = {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            cpf: '',
            telefone: '',
            dob: '',
            cep: '',
            logradouro: '',
            complemento: '',
            bairro: '',
            localidade: '',
            uf: '',
            role: ''
        };
    };

    const submit = () => {
            form.post(route('register'), {
                onSuccess: () => {
                    resetForm();  // Chama a função de reset após o sucesso na submissão
                    alert('Usuário registrado com sucesso!');
                },
                onError: () => {
                    alert('Erro ao tentar registrar o usuário.');
                }
            });
        };
      return {
        users,
        fetchUsers,
        erroCep,
        resetForm,
        modalVisible,
        isSidebarHidden,
        fetchAddress,
        toggleSidebar,
        selectedSection,
        selectSection,
        contacts,
        form,
        submit,
        contact
      };
    }
  }
  </script>
  <style>
  body {
    font-family: 'Nunito', sans-serif;
    margin: 0;
    background-color: #f8fafc;
    color: #636b6f;
    font-weight: 100;
  }

  .new-page {
    display: flex;
  }

  .sidebar {
    width: 250px;
    background-color: #474a59;
    color: white;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    transition: transform 0.3s ease;
  }

  .sidebar.hidden {
    transform: translateX(-100%);
  }

  .logo-container {
    text-align: left;
    margin-bottom: 30px;
    margin-top: 20px;
  }

  .logo-container .logo {
    font-size: 24px;
    font-weight: bold;
    margin-top: -3px;
  }

  .logo-container .psi {
    color: #89ffdb;
  }

  .user-info {
    text-align: center;
    margin-bottom: 30px;
  }

  .user-icon {
    font-size: 40px;
    margin-bottom: 10px;
  }

  .nav-menu ul {
    list-style: none;
    padding: 0;
  }

  .nav-menu ul li {
    padding: 10px 0;
    cursor: pointer;
  }

  .nav-menu ul li.active {
    font-weight: bold;
  }

  .nav-menu ul li:hover {
    text-decoration: underline;
  }

  .main-content {
    margin-left: 250px;
    padding: 20px;
    width: 100%;
    transition: margin-left 0.3s ease;
  }

  .main-content .toggle-button {
    position: absolute;
    top: 20px;
    left: 20px;
    border: none;
    background: none;
    cursor: pointer;
  }

  .main-content .toggle-button:focus {
    outline: none;
  }

  .main-content .section-title {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
  }

  .patients-list {
    list-style: none;
    padding: 0;
  }

  .patient-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
  }

  .circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #474a59;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-right: 15px;
  }

  .info {
    flex-grow: 1;
  }

  .record-button {
    padding: 8px 12px;
    background-color: #474a59;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .record-button:hover {
    background-color: #333640;
  }

  .patient-record {
    margin-top: 40px;
  }

  .patient-record h3 {
    margin-bottom: 20px;
  }

  .patient-record .form-row {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 15px;
    width: 100%;
  }

  .patient-record .form-row label {
    margin-bottom: 5px;
    width: 100%;
  }

  .patient-record .form-row textarea {
    width: 100%;
    padding: 8px;
    background-color: #f8f8f8;
    border: 1px solid #ccc;
    border-radius: 5px;
    resize: vertical;
  }

  .save-button {
    padding: 10px 15px;
    background-color: #474a59;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
  }

  .save-button:hover {
    background-color: #333640;
  }
  .download-button {
    margin-top: 20px;
    padding: 10px 15px;
    background-color: #474a59;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  .download-button:hover {
    background-color: #333640;
  }
  .modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  padding: 20px;
  z-index: 1000;
  width: 90%;  /* Ajuste a largura conforme necessário */
  max-width: 600px;  /* Garante que o modal não fique muito largo em telas grandes */
  overflow: auto;  /* Garante que todo o conteúdo seja rolável se exceder a altura da viewport */
  border-radius: 8px;  /* Cantos arredondados para um visual mais suave */
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);  /* Sombra sutil para profundidade */
}

form {
  background: white;
  max-width: 500px; /* Largura máxima do formulário */
  margin: 30px auto; /* Centraliza o formulário na página */
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Sombra para melhorar a visualização */
  border-radius: 8px; /* Bordas arredondadas */
}

.form-row {
  margin-bottom: 15px;
}

.form-row label {
  display: block; /* Faz com que o label ocupe a linha inteira acima do input */
  color: #333;
  margin-bottom: 5px;
}
.form-row select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: white;
}

.form-row input[type="text"],
.form-row input[type="email"],
.form-row input[type="password"],
.form-row input[type="date"] {
  width: 100%; /* Faz os inputs ocuparem todo o espaço disponível */
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

button {
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  background-color: #474a59;
  color: white;
  cursor: pointer;
  margin-right: 10px; /* Espaço entre os botões */
}

button:hover {
  background-color: #333640; /* Escurece o botão quando o mouse está sobre ele */
}

button[type="submit"] {
  background-color: #4CAF50; /* Cor verde para o botão de enviar */
}

button[type="submit"]:hover {
  background-color: #45a049; /* Verde um pouco mais escuro para o hover */
}

button[type="button"] {
  background-color: #f44336; /* Cor vermelha para o botão de cancelar */
}

button[type="button"]:hover {
  background-color: #d32f2f; /* Vermelho um pouco mais escuro para o hover */
}
  </style>
