import axios from '@/plugins/axios';
export default class AppWebClient {
  constructor() {
    this.axios = axios;
  }

  static createFormData(object) {
    const formData = new FormData();
    for (let key in object) if (object[key]) formData.append(key, object[key].toString());
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

  clientes = {
    inserir: async (nome, cpf) => {
      const formData = AppWebClient.createFormData({nome, cpf});
      const {data} =  await this.axios.post('clientes', formData);
      return data;
    },
    listar: async () => {
      const {data} =  await this.axios.get('clientes');
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
    obter: async (id) => {
      const {data} =  await this.axios.get('visualizar_venda', {params: {id}});
      return data;
    },
    deletar: async (id) => {
      const {data} =  await this.axios.delete('vendas', {params: {id}});
      return data;
    },
  };
  vendas_itens = {
    inserir: async (venda, produto, quantidade, preco_unitario) => {
      const formData = AppWebClient.createFormData({venda, produto, quantidade, preco_unitario});
      const {data} =  await this.axios.post('vendas_itens', formData);
      return data;
    },
    atualizar: async (id, quantidade, preco_unitario) => {
      const formData = AppWebClient.createFormData({id, quantidade, preco_unitario});
      const {data} =  await this.axios.put('vendas_itens', formData);
      return data;
    },
    deletar: async (id) => {
      const {data} =  await this.axios.delete('vendas', {params: {id}});
      return data;
    },
  };
}