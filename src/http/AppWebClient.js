import axios from '@/plugins/axios';
export default class AppWebClient {
  constructor() {
    this.axios = axios;
  }

  #createFormData(object) {
    const formData = new FormData();
    for (let key in object) formData.append(key, object[key]);
    return formData;
  }

  /**
   * Cria uma sessao de usuario, retornando o token JWT.
   * @param {string} usuario
   * @param {string} senha
   */
  async login(usuario, senha) {
    const formData = this.#createFormData({usuario, senha});
    const {data} =  await this.axios.post('login', formData);
    return data;
  }

  vendas = {
    inserir: async (cliente) => {
      const formData = this.#createFormData({cliente});
      const {data} =  await this.axios.post('vendas', formData);
      return data;
    },
    listar: async () => {
      const {data} =  await this.axios.get('vendas');
      return data;
    },
  };
}