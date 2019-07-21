<template>
  <div class="row">
    <div class="col-md-12">
      <button type="button" class="btn btn-outline-primary btn-sm mb-4" data-toggle="modal" data-target="#createArticleModal">
        Crear artículo
      </button>
      <div class="card-columns">
        <div class="card" v-for="article in articles">
          <img class="card-img-top" :src="article.image">
          <div class="card-body">
            <h5 class="card-title">{{ article.title }}</h5>
            <p class="card-text">{{ article.body.split('.')[0] + '.' }}</p>
            <p class="card-text"><small class="text-muted">{{ article.created_at }}</small></p>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="createArticleModal" tabindex="-1" role="dialog" aria-labelledby="createArticleModal" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <form @submit.prevent="createArticle">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Nuevo artículo</h5>
              <button type="button" class="btn btn-outline-success btn-sm ml-auto" @click="generateArticle();">Generar artículo</button>
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
              <button @click="dismissModal();" type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button :disabled="form.busy" type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>
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
        query: {},
        form: new Form({
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
      createArticle(){
        this.$Progress.start();
        this.form.post("api/article")
          .then(({ data }) => {
            this.loadArticles();
            this.$Progress.finish();
            this.dismissModal();
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
      dismissModal(){
        this.form.reset();
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
