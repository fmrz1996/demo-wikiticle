<template>
  <div class="row">
    <div class="col-md-12">
      <button @click="createModal()" type="button" class="btn btn-outline-primary btn-sm mb-4">
        Crear artículo
      </button>
      <div class="card-columns">
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
            <p class="card-text"><small class="text-muted">{{ article.created_at }}</small></p>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="createArticleModal" tabindex="-1" role="dialog" aria-labelledby="createArticleModal" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <form @submit.prevent="editMode ? updateArticle() : createArticle()">
            <div class="modal-header">
              <h5 v-if="!editMode" class="modal-title">Nuevo artículo</h5>
              <h5 v-else class="modal-title">Editar artículo</h5>
              <button v-if="!editMode" type="button" class="btn btn-outline-success btn-sm ml-auto" @click="generateArticle();">Generar artículo</button>
              <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label for="title">Título</label>
                  <input v-model="form.title" type="text" class="form-control" :class="{'is-invalid': form.errors.has('title')}" id="title" placeholder="Ingresa un título...">
                  <has-error :form="form" field="title"></has-error>
                </div>
                <div class="form-group">
                  <label for="image">Imagen</label>
                  <input v-model="form.image" type="text" class="form-control" :class="{'is-invalid': form.errors.has('image')}" id="image" placeholder="Ingresa una imagen...">
                  <has-error :form="form" field="image"></has-error>
                </div>
                <div class="form-group">
                  <label for="body">Contenido</label>
                  <textarea v-model="form.body" class="form-control" :class="{'is-invalid': form.errors.has('body')}" id="body" placeholder="Ingresa un contenido..." rows="5"></textarea>
                  <has-error :form="form" field="body"></has-error>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button v-if="!editMode" :disabled="form.busy" type="submit" class="btn btn-primary">Guardar</button>
              <button v-else :disabled="form.busy" type="submit" class="btn btn-primary">Actualizar</button>
            </div>
          </form>
        </div>
      </div>
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
  Vue.component(HasError.name, HasError)
  Vue.component(AlertError.name, AlertError)

  export default {
    data() {
      return {
        articles: {},
        dataId: null,
        editMode: false,
        query: {},
        form: new Form({
          id: '',
          title: '',
          image: '',
          body: ''
        })
      }
    },
    methods: {
      loadArticles(){
        axios.get("api/article").then(({ data }) => (this.articles = data.data));
      },
      generateArticle(){
        axios.get("https://cors-anywhere.herokuapp.com/es.wikipedia.org/w/api.php?format=json&action=query&generator=random&grnnamespace=0&prop=extracts|pageimages&exchars=1000&pithumbsize=550", {
            headers: {"X-Requested-With": "XMLHttpRequest"}
          }).then(({ data }) => {
            console.log(data);
            for (var pageId in data.query.pages) {
              if (data.query.pages.hasOwnProperty(pageId)) {
                  this.form.title = data.query.pages[pageId].title;
                  this.form.image = data.query.pages[pageId].thumbnail.source;
                  this.form.body = this.stripTags(data.query.pages[pageId].extract);
              }
            }
          });
      },
      createModal(){
        this.editMode = false,
        this.form.reset();
        $('#createArticleModal').modal('show');
      },
      editModal(article){
        this.dataId = article.id;
        this.editMode = true,
        this.form.reset();
        $('#createArticleModal').modal('show');
        this.form.fill(article);
      },
      deleteModal(id){
        this.dataId = id;
        $('#deleteArticleModal').modal('show');
      },
      createArticle(){
        this.$Progress.start();
        this.form.post("api/article").then(() => {
          this.loadArticles();
          this.$Progress.finish();
          $("#createArticleModal .close").click();
          toast.fire({
            type: 'success',
            title: '¡Artículo creado exitosamente!'
          });
        })
        .catch(({ error }) => {
          this.$Progress.fail();
        });
      },
      updateArticle(){
        this.$Progress.start();
        this.form.put("api/article/" + this.form.id).then(() => {
          this.loadArticles();
          this.$Progress.finish();
          $("#createArticleModal .close").click();
          toast.fire({
            type: 'success',
            title: '¡Artículo actualizado exitosamente!'
          });
        })
        .catch(({ error }) => {
          this.$Progress.fail();
        });
      },
      deleteArticle(){
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
      stripTags(html){
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
      }
    },
    created() {
        this.loadArticles();
    }
  }
</script>
