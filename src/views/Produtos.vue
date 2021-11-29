<template>
  <async-container :loading="loading">
    <v-card>
      <v-card-title class="justify-space-between">
        Produtos
        <v-btn color="success" small depressed @click="dialogEditor = true">Criar</v-btn>
      </v-card-title>
      <v-card-text>
        <v-text-field label="Pesquisar" v-model="iptSearch" dense outlined prepend-inner-icon="mdi-magnify" autofocus hide-details></v-text-field>
      </v-card-text>
      <v-data-table
        :items="tableItems"
        :headers="tableHeaders"
        :search="iptSearch"
        sort-by="nome"
        no-data-text="Nenhum produto no momento"
        no-results-text="Nada encontrado"
      >
        <template v-slot:item.preco="{item}">
          R$ {{item.preco.toFixed(2).replace('.',',')}}
        </template>
        <template v-slot:item.actions="{item}">
          <v-btn icon color="amber" small>
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon color="red" small @click="apagarProduto(item)" :loading="loadingBtnApagar.includes(item.id)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
      <v-dialog width="500" max-width="96%" v-model="dialogEditor" persistent>
        <v-card>
          <v-form ref="form-editor" @submit.prevent="salvarProduto" :disabled="loadingEditor">
            <v-card-title>{{iptId ? 'Editar produto' : 'Criar produto'}}</v-card-title>
            <v-card-text>
              <v-text-field label="Nome" v-model="iptNome" :rules="[rules.campoObrigatorio]"></v-text-field>
              <v-text-field label="Preço" v-model="iptPreco" v-money="money" :rules="[rules.campoObrigatorio, rules.campoMonetario]"></v-text-field>
              <v-text-field label="Código do produto" v-model="iptCodigo" hint="Números do código de barras" persistent-hint></v-text-field>
            </v-card-text>
            <v-card-actions class="justify-center">
              <v-btn small color="primary" depressed type="submit" :loading="loadingEditor">Salvar</v-btn>
              <v-btn small color="primary" outlined @click="dialogEditor = false" :disabled="loadingEditor">Fechar</v-btn>
            </v-card-actions>
          </v-form>
        </v-card>
      </v-dialog>
    </v-card>
  </async-container>
</template>

<script>
import AsyncContainer from '@/components/AsyncContainer';
import StringHelper from '@/helpers/StringHelper';
import AppWebClient from '@/http/AppWebClient';
import {VMoney} from 'v-money';
export default {
  name: 'Produtos',
  components: {AsyncContainer},
  data: () => ({
    loading: true,
    tableItems: [],
    tableHeaders: [
      {value: 'nome', text: 'Nome'},
      {value: 'preco', text: 'Preço unitário', align: 'right', filterable: false},
      {value: 'actions', text: '', align: 'right', filterable: false, sortable: false},
    ],
    iptSearch: '',
    dialogEditor: false,
    iptId: null,
    iptNome: null,
    iptPreco: null,
    iptCodigo: null,
    money: {
      decimal: ',',
      thousands: '.',
      prefix: 'R$ ',
      suffix: '',
      precision: 2,
      masked: false /* doesn't work with directive */
    },
    loadingEditor: false,
    loadingBtnApagar: [],
    rules: {
      campoObrigatorio: v => !!v || 'Campo obrigatório',
      campoMonetario: v => v === 'R$ 0,00' || StringHelper.monetaryToDouble(v) < 10000 || 'Insira um valor adequado',
    },
  }),
  methods: {
    async loadData() {
      try {
        const appWebClient = new AppWebClient();
        this.tableItems = await appWebClient.produtos.listar();
      } finally {
        this.loading = false;
      }
    },
    preencherCampos(produto) {
      this.iptId = produto.id;
      this.iptNome = produto.nome;
      this.iptPreco = produto.preco;
      this.iptCodigo = produto.codigo;
    },
    async salvarProduto() {
      if (!this.$refs['form-editor'].validate()) return;
      this.loadingEditor = true;
      try {
        const appWebClient = new AppWebClient();
        if (this.iptId) await appWebClient.produtos.atualizar(this.iptId, this.iptNome, StringHelper.monetaryToDouble(this.iptPreco), this.iptCodigo);
        else await appWebClient.produtos.inserir(this.iptNome, StringHelper.monetaryToDouble(this.iptPreco), this.iptCodigo);
        await this.loadData();
        this.dialogEditor = false;
      } finally {
        this.loadingEditor = false;
      }
    },
    async apagarProduto(produto) {
      if (!confirm(`Tem certeza que quer apagar "${produto.nome}"?`)) return;
      if (!this.loadingBtnApagar.includes(produto.id)) this.loadingBtnApagar.push(produto.id);
      try {
        const appWebClient = new AppWebClient();
        await appWebClient.produtos.deletar(produto.id);
        await this.loadData();
      } finally {
        this.loadingBtnApagar = this.loadingBtnApagar.filter(i => i !== produto.id);
      }
    }
  },
  created() {
    this.loadData();
  },
  watch: {
    dialogEditor(v) {
      if (!v) {
        this.iptId = null;
        this.iptNome = '';
        this.iptPreco = '';
        this.iptCodigo = '';
      }
    },
  },
  directives: {
    money: VMoney
  },
}
</script>
