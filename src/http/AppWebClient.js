import axios from '@/plugins/axios';
export default class AppWebClient {
  constructor() {
    this.axios = axios;
  }

  static createFormData(object) {
    const formData = new FormData();
    for (let key in object) formData.append(key, object[key].toString());
    return formData;
  }

  /**
   * Cria uma sessao de usuario, retornando o token JWT.
   * @param {string} usuario
   * @param {string} senha
   */
  async login(usuario, senha) {
    const formData = AppWebClient.createFormData({usuario, senha});
    const {data} =  await this.axios.post('login', formData);
    return data;
  }

  vendas = {
    inserir: async (cliente) => {
      const formData = AppWebClient.createFormData({cliente});
      const {data} =  await this.axios.post('vendas', formData);
      return data;
    },
    listar: async () => {
      const {data} =  await this.axios.get('vendas');
      return data;
    },
  };

  produtos = {
    inserir: async (nome, preco, codigo) => {
      const formData = AppWebClient.createFormData({nome, preco, codigo});
      const {data} =  await this.axios.post('produtos', formData);
      return data;
    },
    atualizar: async (id, nome, preco, codigo) => {
      const formData = AppWebClient.createFormData({id, nome, preco, codigo});
      const {data} =  await this.axios.post('produtos', formData);
      return data;
    },
    listar: async () => {
      const {data} =  await this.axios.get('produtos');
      return data;
    },
    deletar: async (id) => {
      await this.axios.delete('produtos', {params: {id}});
    },
  };
}