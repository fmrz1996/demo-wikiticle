<template>
  <div class="jumbotron animated fade-in">
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
    </div>
    <div v-else>
      <p>Por desgracia, ¡no existe el artículo requerido!</p>
    </div>
  </div>
</template>

<script>
  import moment from 'moment';

  export default {
    data() {
      return {
        article: {}
      }
    },
    filters: {
      formatTime: (value) => {
        if (value) {
          return moment(String(value)).format('DD/MM/YYYY LT');
        }
      }
    },
    created() {
      axios.get("/api/article/" + this.$route.params.id).then(({ data }) => (this.article = data));
    }
  }
</script>
