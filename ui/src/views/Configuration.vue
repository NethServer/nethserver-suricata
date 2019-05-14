<!--
/*
 * Copyright (C) 2019 Nethesis S.r.l.
 * http://www.nethesis.it - nethserver@nethesis.it
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see COPYING.
 */
-->

<template>
  <div>
    <h2>{{ $t('configuration.title') }}</h2>
    <doc-info
      :placement="'top'"
      :title="$t('docs.ips')"
      :chapter="'suricata'"
      :section="''"
      :inline="false"
    ></doc-info>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-show="view.isLoaded">
      <h3 v-if="configuration.status == 'enabled'">{{ $t('configuration.ips') }}</h3>

      <div v-if="configuration.status == 'disabled'" class="blank-slate-pf">
        <h1>{{$t('configuration.ips_is_disabled')}}</h1>
        <p>{{$t('configuration.ips_is_disabled_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="saveConfiguration(true)"
            class="btn btn-primary btn-lg"
          >{{$t('configuration.enable_ips')}}</button>
        </div>
      </div>
      <div v-if="configuration.status == 'enabled'" class="panel panel-default">
        <div class="panel-heading">
          <toggle-button
            class="min-toggle right"
            :width="40"
            :height="20"
            :color="{checked: '#0088ce', unchecked: '#bbbbbb'}"
            :value="configuration.status == 'enabled'"
            :sync="true"
            @change="saveConfiguration(true)"
          />
          <span class="panel-title">
            {{$t('configuration.enabled')}}
            <span
              :class="['fa', configuration.status == 'enabled' ? 'fa-check green' : 'fa-times red']"
            ></span>
          </span>
        </div>
      </div>

      <h3 v-if="allCategories.length > 0">{{$t('configuration.available_categories')}}</h3>
      <v-select
        v-if="allCategories.length > 0"
        @input="addCategory"
        v-model="categoryToAdd"
        :options="allCategories"
        label="Description"
        :clearable="false"
        :placeholder="$t('configuration.select_category')"
        :components="{OpenIndicator}"
        id="cat-search"
      >
        <template slot="option" slot-scope="option">{{ option.Description }}</template>
        <div slot="no-options">{{$t('configuration.category_not_found')}}</div>
      </v-select>
      <h4 v-show="newCategories.length > 0">{{$t('configuration.selected_categories')}}</h4>
      <div class="row adjust-mg-bottom" v-for="(c, ck) in newCategories" :key="ck">
        <span class="col-sm-4">{{c.Description}}</span>
        <span class="col-sm-2">
          <select class="form-control" v-model="c.type">
            <option value="alert">{{$t('configuration.alert')}}</option>
            <option value="block">{{$t('configuration.block')}}</option>
          </select>
        </span>
        <span class="col-sm-2">
          <span class="fa fa-times remove-cat" @click="removeCategory(ck)"></span>
        </span>
      </div>
      <button
        v-if="newCategories.length > 0"
        class="btn btn-primary"
        @click="addCategoriesToConfig()"
      >{{newCategories.length == 1 ? $t('configuration.save_category') : $t('configuration.save_categories')}}</button>
      <div v-if="newCategories.length > 0" class="divider adjust-divider"></div>

      <h3
        v-if="configuration.BlockCategories.length > 0 || configuration.AlertCategories.length > 0"
      >
        {{$t('configuration.configured_categories')}}
        <button
          v-if="changesNeeded > 0"
          @click="saveConfiguration()"
          class="btn btn-primary right"
        >{{$t('configuration.update_items')}}</button>
      </h3>
      <div
        v-if="changesNeeded == 0 && configuration.BlockCategories.length == 0 && configuration.AlertCategories.length == 0"
        class="blank-slate-pf"
      >
        <div class="blank-slate-pf-icon">
          <span class="fa fa-ban"></span>
        </div>
        <h1>{{allCategories.length > 0 ? $t('configuration.no_categories_configured') : $t('configuration.categories_not_found')}}</h1>
        <p>{{allCategories.length > 0 ? $t('configuration.no_categories_configured_desc') : $t('configuration.categories_not_found_desc') }}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="allCategories.length > 0 ? focusSearch() : downloadCategories()"
            class="btn btn-primary btn-lg"
          >{{allCategories.length > 0 ? $t('configuration.search') : $t('configuration.download')}}</button>
        </div>
      </div>
      <div
        v-if="changesNeeded > 0 && configuration.BlockCategories.length == 0 && configuration.AlertCategories.length == 0"
        class="blank-slate-pf"
      >
        <div class="blank-slate-pf-icon">
          <span class="fa fa-ban"></span>
        </div>
        <h1>{{$t('configuration.no_categories_configured')}}</h1>
        <p>{{$t('configuration.no_categories_applied_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="saveConfiguration()"
            class="btn btn-primary btn-lg"
          >{{$t('configuration.update_items')}}</button>
        </div>
      </div>

      <h4 v-if="configuration.BlockCategories.length > 0">{{$t('configuration.block')}}</h4>
      <div
        v-if="configuration.BlockCategories.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <div
          v-for="(m, mk) in configuration.BlockCategories"
          v-bind:key="mk"
          class="list-group-item"
        >
          <div class="list-view-pf-actions">
            <button
              @click="removeCategoryConfig(configuration.BlockCategories, mk)"
              class="btn btn-danger"
            >{{$t('configuration.disable')}}</button>
          </div>

          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span :class="['fa', 'list-view-pf-icon-sm', 'fa-ban']"></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading">{{m.name}}</div>
                <div class="list-group-item-text">{{m.Description}}</div>
              </div>
              <div class="list-view-pf-additional-info rules-info">
                <div class="list-view-pf-additional-info-item">
                  <span class="pficon pficon-security"></span>
                  <select
                    @change="updateCategoryConfig(configuration.BlockCategories, mk, m.type)"
                    class="form-control"
                    v-model="m.type"
                  >
                    <option value="alert">{{$t('configuration.alert')}}</option>
                    <option value="block">{{$t('configuration.block')}}</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <h4 v-if="configuration.AlertCategories.length > 0">{{$t('configuration.alert')}}</h4>
      <div
        v-if="configuration.AlertCategories.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <div
          v-for="(m, mk) in configuration.AlertCategories"
          v-bind:key="mk"
          class="list-group-item"
        >
          <div class="list-view-pf-actions">
            <button
              @click="removeCategoryConfig(configuration.AlertCategories, mk)"
              class="btn btn-danger"
            >{{$t('configuration.disable')}}</button>
          </div>

          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span :class="['fa', 'list-view-pf-icon-sm', 'fa-ban']"></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading">{{m.name}}</div>
                <div class="list-group-item-text">{{m.Description}}</div>
              </div>
              <div class="list-view-pf-additional-info rules-info">
                <div class="list-view-pf-additional-info-item">
                  <span class="fa fa-exclamation-triangle"></span>
                  <select
                    @change="updateCategoryConfig(configuration.AlertCategories, mk, m.type)"
                    class="form-control"
                    v-model="m.type"
                  >
                    <option value="alert">{{$t('configuration.alert')}}</option>
                    <option value="block">{{$t('configuration.block')}}</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="routeChangeModal" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">{{$t('configuration.not_applied_changes_title')}}</h4>
          </div>
          <form class="form-horizontal" v-on:submit.prevent="proceed()">
            <div class="modal-body">
              <div class="form-group">
                <label
                  class="col-sm-10 control-label"
                  for="textInput-modal-markup"
                >{{$t('configuration.not_applied_changes')}}?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" type="button" data-dismiss="modal">{{$t('cancel')}}</button>
              <button class="btn btn-primary" type="submit">{{$t('leave')}}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var nethserver = window.nethserver;
var console = window.console;

export default {
  name: "Configuration",
  mounted() {
    this.getConfiguration();
    this.getCategories();
  },
  beforeRouteLeave(to, from, next) {
    if (this.changesNeeded > 0) {
      $("#routeChangeModal").modal("show");
      this.proceedFunc = next;
    } else {
      next();
    }
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      allCategories: [],
      configuration: {
        BlockCategories: [],
        AlertCategories: [],
        status: ""
      },
      newCategories: [],
      categoryToAdd: {},
      changesNeeded: 0,
      OpenIndicator: {
        render: createElement =>
          createElement("span", { class: { toggle: true } })
      }
    };
  },
  methods: {
    proceed() {
      $(".modal").modal("hide");
      this.proceedFunc();
    },
    focusSearch() {
      $("#cat-search")
        .find("input")
        .focus();
    },
    getConfiguration() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-suricata/configuration/read"],
        {
          action: "configuration"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.configuration = success;

          for (var c in context.configuration.BlockCategories) {
            var cat = context.configuration.BlockCategories[c];
            cat.type = "block";
          }
          for (var c in context.configuration.AlertCategories) {
            var cat = context.configuration.AlertCategories[c];
            cat.type = "alert";
          }

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    getCategories() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-suricata/configuration/read"],
        {
          action: "categories"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.allCategories = success.categories;
          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    },
    downloadCategories() {
      var context = this;

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "configuration.categories_downloaded_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "configuration.categories_downloaded_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-suricata/configuration/update"],
        {
          action: "download"
        },
        function(stream) {
          console.info("download", stream);
        },
        function(success) {
          // get all
          context.getConfiguration();
          context.getCategories();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    addCategory(category) {
      if (category && category.name.length > 0) {
        if (!this.categoryAlreadyAdded(category)) {
          category.type = "alert";
          this.newCategories.push(category);
        }
      }
    },
    categoryAlreadyAdded(category) {
      return this.newCategories.indexOf(category) > -1;
    },
    removeCategory(index) {
      this.newCategories.splice(index, 1);
    },
    removeCategoryConfig(data, index) {
      data.splice(index, 1);

      this.changesNeeded++;
    },
    updateCategoryConfig(data, index, newState) {
      var old = data[index];

      switch (newState) {
        case "alert":
          this.configuration.AlertCategories.push(old);
          this.configuration.BlockCategories.splice(index, 1);
          break;

        case "block":
          this.configuration.BlockCategories.push(old);
          this.configuration.AlertCategories.splice(index, 1);
          break;
      }

      this.changesNeeded++;
    },
    addCategoriesToConfig() {
      for (var c in this.newCategories) {
        var cat = this.newCategories[c];

        switch (cat.type) {
          case "alert":
            this.configuration.AlertCategories.push(cat);
            break;

          case "block":
            this.configuration.BlockCategories.push(cat);
            break;
        }
      }
      this.categoryToAdd = {};
      this.newCategories = [];
      this.saveConfiguration();
    },
    saveConfiguration(toggle) {
      var context = this;

      if (toggle) {
        context.configuration.status =
          context.configuration.status == "enabled" ? "disabled" : "enabled";
      }

      // notification
      nethserver.notifications.success = context.$i18n.t(
        "configuration.configuration_updated_ok"
      );
      nethserver.notifications.error = context.$i18n.t(
        "configuration.configuration_updated_error"
      );

      // update values
      nethserver.exec(
        ["nethserver-suricata/configuration/update"],
        {
          action: "configuration",
          status: context.configuration.status,
          BlockCategories: context.configuration.BlockCategories.map(function(
            c
          ) {
            return c.name;
          }),
          AlertCategories: context.configuration.AlertCategories.map(function(
            c
          ) {
            return c.name;
          })
        },
        function(stream) {
          console.info("configuration", stream);
        },
        function(success) {
          // get all
          context.changesNeeded = 0;
          context.getConfiguration();
          context.getCategories();
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    }
  }
};
</script>

<style>
.list-group-item-heading {
  width: calc(50% - 20px) !important;
}
.list-group-item-text {
  width: calc(50% - 40px) !important;
}
.list-view-pf-description {
  flex: 1 0 70% !important;
}
.list-view-pf-actions {
  z-index: 2;
}
.remove-cat {
  margin-top: 6px;
  color: dimgrey;
}
.remove-cat:hover {
  cursor: pointer;
  color: #39a5dc;
}

.adjust-mg-bottom {
  margin-bottom: 4px;
}
.adjust-divider {
  margin-top: 15px;
}
</style>
