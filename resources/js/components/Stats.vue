<template>
  <div class="row">
    <app-loading v-if="loading"></app-loading>
    <div v-else class="container recent-activity animated fade-in">
      <div class="row mb-4">
        <div class="col-12">
          <h3>Actividad reciente</h3>
          <line-chart v-if="apiLoaded" :styles="styleChart"></line-chart>
        </div>
      </div>
      <hr>
      <div class="row mb-4">
        <div class="col-12">
          <h3 class="mb-3">Artículos más populares</h3>
          <table class="table table-hover">
            <thead class="thead-light">
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Visitas</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(article, index) in articles.view">
                <th>{{ index + 1 }}</th>
                <td>
                  <router-link :to="`/articles/${article.id}`">
                    {{ article.title }}
                  </router-link>
                </td>
                <td>{{ article.created_at | formatTime }}</td>
                <td>{{ article.pageviews }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-md-6">
          <h5 class="mb-3">Artículos más cortos</h5>
          <table class="table table-hover">
            <thead class="thead-light">
              <tr>
                <th>Nombre</th>
                <th>Caracteres</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(article, index) in articles.lengthAsc">
                <td>
                  <router-link :to="`/articles/${article.id}`">
                    {{ article.title }}
                  </router-link>
                </td>
                <td>{{ article.body.length }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-6">
          <h5 class="mb-3">Artículos más largos</h5>
          <table class="table table-hover">
            <thead class="thead-light">
              <tr>
                <th>Nombre</th>
                <th>Caracteres</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(article, index) in articles.lengthDesc">
                <td>
                  <router-link :to="`/articles/${article.id}`">
                    {{ article.title }}
                  </router-link>
                </td>
                <td>{{ article.body.length }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import LineChart from '../LineChart.vue';
  import moment from 'moment';

  export default {
    data() {
      return {
        articles: {
          view: {},
          lengthAsc: {},
          lengthDesc: {}
        },
        apiLoaded: false,
        loading: true
      }
    },
    components: {
      LineChart
    },
    computed: {
      styleChart() {
        return {
          height: '400px',
          position: 'relative'
        }
      }
    },
    filters: {
      formatTime: (value) => {
        if (value) {
          return moment(String(value)).format('DD/MM/YYYY LT');
        }
      }
    },
    methods: {
      loadArticles() {
        axios.get("api/stats/1").then(({ data }) => {
          this.articles.view = data;
        });
        axios.get("api/stats/2").then(({ data }) => {
          this.articles.lengthAsc = data;
        });
        axios.get("api/stats/3").then(({ data }) => {
          this.articles.lengthDesc = data;
          this.apiLoaded = true;
          this.loading = false;
        });
      }
    },
    created() {
      this.loadArticles();
    }
  }
</script>
