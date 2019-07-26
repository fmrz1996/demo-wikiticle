<template>
  <div class="jumbotron animated fade-in">
    <h1 class="display-4">{{ article.title }}</h1>
    <p class="lead">{{ article.created_at }}</p>
    <hr class="my-4">
    <div class="row">
      <div class="col-md-4">
        <img class="card-img-top" :src="article.image">
      </div>
      <div class="col-md-8">
        <p class="text-justify">{{ article.body }}</p>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        article: {}
      }
    },
    created() {
      axios.get("/api/article/" + this.$route.params.id).then(({ data }) => (this.article = data));
    }
  }
</script>
