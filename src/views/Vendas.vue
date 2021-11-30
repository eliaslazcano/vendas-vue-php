<template>
  <async-container :loading="loading">
    <v-card>
      <v-card-title class="justify-space-between">
        Vendas
        <v-btn color="success" small depressed @click="dialogCriarVenda = true">Criar</v-btn>
      </v-card-title>
      <v-data-table
        :headers="tableHeaders"
        :items="vendas"
        no-data-text="Nenhuma venda registrada"
        no-results-text="Nenhuma venda encontrada"
        sort-by="id"
        sort-desc
      >
        <template v-slot:item.cliente="{item}">
          <p class="subtitle-1 mb-0" v-if="item.cliente">{{item.cliente_nome}}</p>
          <p class="text--disabled mb-0" v-else>Anonimo</p>
        </template>
        <template v-slot:item.credito="{item}">
          <v-chip color="success" label small v-if="item.credito">R$ {{item.credito.toFixed(2)}}</v-chip>
          <v-chip color="error" label small v-else>NÃO</v-chip>
        </template>
        <template v-slot:item.criado_em="{item}">{{formatarDatetime(item.criado_em)}}</template>
        <template v-slot:item.actions="{item}">
          <v-btn icon color="amber" small @click="visualizarVenda(item.id)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon color="red" small @click="deletarVenda(item)" :loading="loadingBtnApagar.includes(item.id)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog width="500" max-width="96%" v-model="dialogCriarVenda">
      <v-card>
        <v-form ref="form-venda" @submit.prevent="criarVenda" :disabled="loadingCriarVenda">
          <v-card-title>Criar uma venda</v-card-title>
          <v-card-text>
            <v-autocomplete
              label="Cliente (nome)"
              v-model="iptCliente"
              no-data-text="Nenhum cliente conhecido"
              item-value="id"
              item-text="nome"
              :items="[{id: null, nome: 'Anônimo'}, ...iptClienteItems]"
              cache-items
            >
              <template v-slot:append-outer>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="success" icon depressed small v-bind="attrs" v-on="on" @click="dialogCriarCliente = true">
                      <v-icon>mdi-plus-circle</v-icon>
                    </v-btn>
                  </template>
                  <span>Criar Cliente</span>
                </v-tooltip>
              </template>
            </v-autocomplete>
          </v-card-text>
          <v-card-actions class="justify-center">
            <v-btn small color="primary" depressed type="submit" :loading="loadingCriarVenda">Salvar</v-btn>
            <v-btn small color="primary" outlined @click="dialogCriarVenda = false" :disabled="loadingCriarVenda">Fechar</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
    <v-dialog width="500" max-width="96%" v-model="dialogCriarCliente">
      <v-card>
        <v-form ref="form-cliente" @submit.prevent="criarCliente" :disabled="loadingCriarCliente">
          <v-card-text>
            <p class="title">Criar cliente</p>
            <v-text-field label="Nome do cliente" v-model="iptClienteNome"></v-text-field>
            <v-text-field label="CPF do cliente" v-model="iptClienteCpf"></v-text-field>
          </v-card-text>
          <v-card-actions class="justify-center">
            <v-btn small color="primary" depressed type="submit" :loading="loadingCriarCliente">Salvar</v-btn>
            <v-btn small color="primary" outlined @click="dialogCriarCliente = false" :disabled="loadingCriarCliente">Fechar</v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>
    <v-dialog width="720" max-width="96%" v-model="dialogVenda">
      <v-card v-if="venda">
        <v-card-title class="blue white--text justify-space-between">
          Venda nº{{venda.id}}
          <v-icon size="32" @click="dialogVenda = false" color="white">mdi-close</v-icon>
        </v-card-title>
        <hr class="mb-3"/>
        <v-card-text>
          <v-text-field
            label="Cliente"
            :value="venda.cliente ? venda.cliente_nome : 'Anônimo'"
            outlined dense readonly
          />
          <v-autocomplete
            label="Adicionar produto"
            item-value="id"
            item-text="nome"
            :items="produtos"
            outlined dense
          />
          <v-card outlined>
            <v-data-table
              dense hide-default-footer
              no-data-text="Nenhum produto adicionado na venda"
              :headers="[{value: 'produto_nome', text: 'Produto'},{value: 'preco_unitario', text: 'Preço Un'},{value: 'quantidade', text: 'Qtd'}, {value: 'valor', text: 'Total'}]"
              :items="venda.itens"
              :items-per-page="-1"
            ></v-data-table>
          </v-card>
        </v-card-text>
      </v-card>
    </v-dialog>
  </async-container>
</template>

<script>
import AsyncContainer from '@/components/AsyncContainer';
import AppWebClient from '@/http/AppWebClient';
import moment from 'moment';
export default {
  name: 'Vendas',
  components: {AsyncContainer},
  data: () => ({
    loading: true,
    loadingCriarVenda: false,
    loadingCriarCliente: false,
    loadingBtnApagar: [],
    dialogCriarVenda: false,
    dialogCriarCliente: false,
    dialogVenda: false,
    tableHeaders: [
      {value: 'id', text: 'Nº', align: 'left', width: '4rem'},
      {value: 'cliente', text: 'Cliente', align: 'left'},
      {value: 'credito', text: 'Pago', align: 'center'},
      {value: 'criado_em', text: 'Data', align: 'center'},
      {value: 'actions', text: '#', align: 'right', filterable: false, sortable: false},
    ],
    vendas: [],
    venda: null,
    produtos: [],
    iptCliente: null, //nulo = anonimo
    iptClienteItems: [],
    iptClienteNome: '',
    iptClienteCpf: '',
    appWebClient: new AppWebClient(),
  }),
  methods: {
    async recarregarVendas() {
      this.vendas = await this.appWebClient.vendas.listar();
    },
    async recarregarClientes() {
      this.iptClienteItems = await this.appWebClient.clientes.listar();
    },
    async recarregarProdutos() {
      this.produtos = await this.appWebClient.produtos.listar();
    },
    async loadData() {
      try {
        await Promise.all([this.recarregarVendas(), this.recarregarClientes()]);
      } finally {
        this.loading = false;
      }
    },
    async criarVenda() {
      if (!this.$refs['form-venda'].validate()) return;
      this.loadingCriarCliente = true;
      try {
        await this.appWebClient.vendas.inserir(this.iptCliente);
        await this.recarregarVendas();
        this.loadingCriarVenda = false;
        //TODO - Abrir o painel da venda
      } finally {
        this.loadingCriarCliente = false;
      }
    },
    async criarCliente() {
      if (!this.$refs['form-cliente'].validate()) return;
      this.loadingCriarCliente = true;
      try {
        await this.appWebClient.clientes.inserir(this.iptClienteNome, this.iptClienteCpf);
        await this.recarregarClientes();
        this.dialogCriarCliente = false;
      } finally {
        this.loadingCriarCliente = false;
      }
    },
    async deletarVenda(venda) {
      const mensagem = venda.cliente_nome ? `Apagar esta venda de ${venda.cliente_nome}?` : 'Apagar esta venda?';
      if (!confirm(mensagem)) return;
      if (!this.loadingBtnApagar.includes(venda.id)) this.loadingBtnApagar.push(venda.id);
      try {
        await this.appWebClient.vendas.deletar(venda.id);
        await this.recarregarVendas();
      } finally {
        this.loadingBtnApagar = this.loadingBtnApagar.filter(i => i !== venda.id);
      }
    },
    async visualizarVenda(id) {
      const promisses = [];
      promisses.push(this.appWebClient.vendas.obter(id));
      if (this.produtos.length === 0) promisses.push(this.recarregarProdutos());
      const resultado = await Promise.all(promisses);
      this.venda = resultado[0];
      if (this.produtos.length === 0) this.produtos = resultado[1];
      this.dialogVenda = true;
    },
    formatarDatetime(datetime) {
      return moment(datetime).format('DD/MM/YYYY HH:mm');
    },
  },
  created() {
    this.loadData();
  },
}
</script>
