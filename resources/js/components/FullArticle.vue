<template>
  <div class="row">
    <div class="col-12">
      <app-article-options :articles="totalArticles"></app-article-options>
    </div>
    <div class="col-12">
        <app-loading v-if="loading"></app-loading>
        <div v-else class="animated fade-in">
          <div class="jumbotron">
            <div v-if="article.length !== 0">
              <h1 class="display-4">{{ article.title }}</h1>
              <p class="lead">{{ article.created_at | formatTime }} -<span class="pageviews ml-1 text-muted">{{ article.pageviews }} visitas en un mes</span></p>
              <hr class="my-4">
              <div class="row">
                <div class="col-md-4">
                  <img class="card-img-top" :src="article.image">
                </div>
                <div class="col-md-8">
                  <p class="text-justify">{{ article.body }}</p>
                  <p class="mt-auto source-article"><span class="font-weight-bold">Fuente: </span><a :href="article.source" target="_blank">{{ article.source }}</a></p>
                </div>
              </div>
              <div class="article-controls">
                <router-link v-if="article.prev_article !== null" :to="`/articles/${article.prev_article}`" class="prev-article text-decoration-none"><i class="fa fa-angle-left fa-4x"></i></router-link>
                <router-link v-if="article.next_article !== null" :to="`/articles/${article.next_article}`" class="next-article text-decoration-none"><i class="fa fa-angle-right fa-4x"></i></router-link>
              </div>
            </div>
            <div v-else>
              <p>Por desgracia, ¡no existe el artículo requerido!</p>
            </div>
          </div>
        </div>
    </div>
  </div>
</template>

<script>
  import moment from 'moment';

  export default {
    data() {
      return {
        article: {},
        loading: true,
        totalArticles: {}
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
      loadFullArticle() {
        axios.get("/api/article/" + this.$route.params.id).then(({ data }) => {
          this.article = data;
          this.loading = false;
        });
      },
      loadArticles() {
        axios.get("/api/article").then(({ data }) => {
          this.totalArticles = data.total;
          console.log(data.total);
        });
      }
    },
    watch: {
      '$route.path': 'loadFullArticle'
    },
    mounted() {
      this.$root.$on('FullArticle', () => {
        this.loadFullArticle();
      });
    },
    created() {
      this.loadFullArticle();
      this.loadArticles();
    }
  }
</script>
