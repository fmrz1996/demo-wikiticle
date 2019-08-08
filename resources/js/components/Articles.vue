<template>
  <div class="row">
    <div class="col-sm-6">
      <app-article-options :articles="articles" :form="form" :editMode="editMode"></app-article-options>
    </div>
    <div class="col-sm-6">
      <paginate v-if="!loading" class="float-right"
        :page-count="dataPage"
        :click-handler="pageCallback"
        :prev-text="'Anterior'"
        :next-text="'Siguiente'"
        :container-class="'pagination pagination-sm'"
        :page-class="'page-item'"
        :page-link-class="'page-link'"
        :prev-class="'page-item'"
        :next-class="'page-item'"
        :prev-link-class="'page-link'"
        :next-link-class="'page-link'">
      </paginate>
    </div>
    <div class="col-12">
      <app-loading v-if="loading"></app-loading>
      <div v-else class="card-columns animated fade-in">
        <div class="card" v-for="article in articles">
          <img class="card-img-top" :src="article.image">
          <div class="card-img-overlay">
            <div class="card-options ml-auto d-table fade">
              <a @click="editModal(article)" class="text-dark pointer"><i class="fa fa-pencil-square-o fa-lg mr-2"></i></a>
              <a @click="deleteModal(article.id)" class="text-dark pointer"><i class="fa fa fa-trash-o fa-lg"></i></a>
            </div>
          </div>
          <div class="card-body">
            <router-link class="card-link" :to="`/articles/${article.id}`">
              <h5 class="card-title">
                {{ article.title }}
              </h5>
            </router-link>
            <p class="card-text">{{ article.body.split('.')[0] + '.' }}</p>
            <p class="card-text"><small class="text-muted"><i class="fa fa-calendar-o mr-1"></i>{{ article.created_at | relativeTime | capitalize }} <span class="ml-1 mr-1">–</span> <i class="fa fa-eye mr-1"></i>{{ article.pageviews }} visitas</small></p>
          </div>
        </div>
      </div>
      <span v-if="articles.length == 0">No hay articulos registrados, ¡empieza por uno ya!</span>
    </div>
    <div class="modal fade" id="deleteArticleModal" tabindex="-1" role="dialog" aria-labelledby="deleteArticleModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar acción</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro que deseas eliminar este artículo?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button @click="deleteArticle();" type="button" class="btn btn-danger">Eliminar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { Form, HasError, AlertError } from 'vform';
  Vue.component(HasError.name, HasError);
  Vue.component(AlertError.name, AlertError);

  import moment from 'moment';
  moment.locale('es');

  export default {
    data() {
      return {
        articles: {},
        dataId: null,
        dataPage: 1,
        editMode: false,
        loading: true,
        form: new Form({
          id: '',
          title: '',
          image: '',
          body: '',
          pageviews: '',
          source: ''
        })
      }
    },
    filters: {
      capitalize: (value) => {
        if (value) {
          return value.charAt(0).toUpperCase() + value.slice(1);
        }
      },
      relativeTime: (value) => {
        if (value) {
          return moment(String(value)).fromNow();
        }
      }
    },
    methods: {
      loadArticles() {
        axios.get("/api/article").then(({ data }) => {
          this.dataPage = data.last_page;
          this.articles = data.data;
          this.loading = false;
        });
      },
      editModal(article) {
        this.$root.$emit('ArticlesOptions', article);
      },
      deleteModal(id) {
        this.dataId = id;
        $('#deleteArticleModal').modal('show');
      },
      deleteArticle() {
        this.$Progress.start();
        this.form.delete("api/article/" + this.dataId).then(() => {
          this.loadArticles();
          this.$Progress.finish();
          $("#deleteArticleModal .close").click();
          toast.fire({
            type: 'success',
            title: '¡Artículo eliminado exitosamente!'
          });
        })
        .catch(({ error }) => {
          this.$Progress.fail();
        });
      },
      pageCallback(pageNum) {
        axios.get("/api/article?page=" + pageNum).then(({ data }) => (this.articles = data.data));
      }
    },
    mounted() {
      this.$root.$on('Articles', () => {
        this.loadArticles();
      });
    },
    created() {
        this.loadArticles();
    }
  }
</script>
