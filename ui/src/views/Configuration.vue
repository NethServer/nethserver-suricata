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

    <div
      v-if="changesNeeded > 0"
      :class="['alert', 'alert-warning', 'alert-dismissable mg-top-10']"
    >
      <button
        @click="saveConfiguration()"
        class="btn btn-primary pull-right mg-left-5"
      >{{$t('configuration.apply_changes')}}</button>
      <button
        @click="getConfiguration()"
        class="btn btn-default pull-right"
      >{{$t('configuration.reset')}}</button>

      <span :class="['pficon', 'pficon-warning-triangle-o']"></span>

      <strong>{{$t('warning')}}.</strong>
      <span class="mg-left-5">{{$t('configuration.configuration_categories_change')}}</span>.
    </div>

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

      <h3>{{$t('configuration.actions')}}</h3>
      <div class="btn-group">
        <button
          @click="resetDefault()"
          class="btn btn-primary btn-lg shutdown-privileged"
          data-action="restart"
          data-container="body"
        >{{$t('configuration.reset_default')}}</button>
        <button
          data-toggle="dropdown"
          class="btn btn-primary btn-lg dropdown-toggle shutdown-privileged"
        >
          <span class="caret"></span>
        </button>
        <ul role="menu" class="dropdown-menu">
          <li class="presentation">
            <a
              @click="enableAll()"
              role="menuitem"
              data-action="shutdown"
            >{{$t('configuration.enable_all')}}</a>
          </li>
          <li class="presentation">
            <a
              @click="disableAll()"
              role="menuitem"
              data-action="restart"
            >{{$t('configuration.disable_all')}}</a>
          </li>
        </ul>
      </div>

      <h3
        v-if="configuration.status == 'enabled' && configuration.categories.length > 0"
      >{{$t('configuration.category_list')}}</h3>
      <form
        v-if="configuration.status == 'enabled' && configuration.categories.length > 0"
        role="form"
        class="search-pf has-button search"
      >
        <div class="form-group has-clear">
          <div class="search-pf-input-group">
            <label class="sr-only">Search</label>
            <input
              v-focus
              type="search"
              v-model="searchString"
              class="form-control input-lg"
              :placeholder="$t('search')+'...'"
              id="pf-search-list"
            >
          </div>
        </div>
      </form>

      <div
        v-if="configuration.status == 'enabled' && configuration.categories.length == 0"
        class="blank-slate-pf"
      >
        <div class="blank-slate-pf-icon">
          <span class="fa fa-ban"></span>
        </div>
        <h1>{{$t('configuration.categories_not_found')}}</h1>
        <p>{{$t('configuration.categories_not_found_desc')}}.</p>
        <div class="blank-slate-pf-main-action">
          <button
            @click="downloadCategories()"
            class="btn btn-primary btn-lg"
          >{{$t('configuration.download')}}</button>
        </div>
      </div>

      <div
        v-if="configuration.status == 'enabled' && configuration.categories.length > 0"
        class="list-group list-view-pf list-view-pf-view no-mg-top mg-top-10"
      >
        <div
          v-for="(m, mk) in filteredCategories"
          v-bind:key="mk"
          :class="['list-group-item', m.status == 'disable' ? 'gray' : '']"
        >
          <div class="list-view-pf-actions">
            <button
              v-if="m.status != 'disable'"
              @click="m.status = 'disable'; changesNeeded++"
              class="btn btn-danger"
            >{{$t('configuration.disable')}}</button>
            <button
              v-if="m.status == 'disable'"
              @click="m.status = 'alert'; changesNeeded++"
              class="btn btn-primary"
            >{{$t('configuration.enable')}}</button>
          </div>

          <div class="list-view-pf-main-info small-list">
            <div class="list-view-pf-left">
              <span
                :class="['fa', 'list-view-pf-icon-sm', m.status == 'block' ? 'pficon pficon-security' : m.status == 'alert' ? 'fa fa-exclamation-triangle' : 'fa fa-ban']"
              ></span>
            </div>
            <div class="list-view-pf-body">
              <div class="list-view-pf-description">
                <div class="list-group-item-heading">
                  {{m.Description}}
                  <span class="gray">({{m.name}}</span>)
                </div>
              </div>
              <div class="list-view-pf-additional-info rules-info">
                <div class="list-view-pf-additional-info-item">
                  <span
                    :class="[m.status == 'block' ? 'pficon pficon-security' : m.status == 'alert' ? 'fa fa-exclamation-triangle' : 'fa fa-ban']"
                  ></span>
                  <select
                    @change="updateStatus(mk, m.status)"
                    class="form-control"
                    v-model="m.status"
                    :disabled="m.status == 'disable'"
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
    this.getDefaultCategories();
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
      searchString: "",
      configuration: {
        categories: [],
        status: ""
      },
      defaultCategories: [],
      changesNeeded: 0
    };
  },
  computed: {
    filteredCategories() {
      var returnObj = [];
      for (var r in this.configuration.categories) {
        var cat = JSON.stringify(this.configuration.categories[r]);
        if (cat.toLowerCase().includes(this.searchString.toLowerCase())) {
          returnObj.push(this.configuration.categories[r]);
        }
      }

      return returnObj;
    }
  },
  methods: {
    proceed() {
      $(".modal").modal("hide");
      this.proceedFunc();
    },
    resetDefault() {
      this.configuration.categories = this.defaultCategories;
      this.changesNeeded++;
    },
    enableAll() {
      for (var c in this.configuration.categories) {
        this.configuration.categories[c].status = "alert";
      }
      this.changesNeeded++;
    },
    disableAll() {
      for (var c in this.configuration.categories) {
        this.configuration.categories[c].status = "disable";
      }
      this.changesNeeded++;
    },
    getDefaultCategories() {
      var context = this;

      nethserver.exec(
        ["nethserver-suricata/configuration/read"],
        {
          action: "default-categories"
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.defaultCategories = success.categories;
        },
        function(error) {
          console.error(error);
        }
      );
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

          context.changesNeeded = 0;
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
        },
        function(error, data) {
          console.error(error, data);
        }
      );
    },
    updateStatus(index, newState) {
      this.configuration.categories[index].status = newState;
      this.changesNeeded++;
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
          categories: context.configuration.categories
        },
        function(stream) {
          console.info("configuration", stream);
        },
        function(success) {
          // get all
          context.changesNeeded = 0;
          context.getConfiguration();
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
