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
    <h2>{{ $t('dashboard.title') }}</h2>

    <div v-if="!view.isLoaded" class="spinner spinner-lg view-spinner"></div>
    <div v-show="view.isLoaded">
      <h2>
        {{$t('dashboard.evebox')}}
        <span
          class="gray min-size right"
        >{{$t('dashboard.last_updated_24h')}}</span>
      </h2>
      <div class="row">
        <div class="col-sm-6">
          <a
            :href="eveboxUrl"
            class="btn btn-primary mg-bottom-10"
            target="_blank"
          >{{$t('dashboard.open_evebox')}}</a>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <h3 class="no-mg-top">{{$t('dashboard.categories')}}</h3>
          <div v-show="Object.keys(alerts && alerts.categories).length == 0" class="empty-piechart">
            <span class="fa fa-pie-chart"></span>
            <div>{{ $t('dashboard.empty_piechart_label') }}</div>
          </div>
          <div v-show="Object.keys(alerts && alerts.categories).length > 0" id="stats-categories-pie-chart"></div>
        </div>
        <div class="col-sm-6">
          <h3 class="no-mg-top">{{$t('dashboard.severities')}}</h3>
          <div v-show="Object.keys(alerts && alerts.severities).length == 0" class="empty-piechart">
            <span class="fa fa-pie-chart"></span>
            <div>{{ $t('dashboard.empty_piechart_label') }}</div>
          </div>
          <div v-show="Object.keys(alerts && alerts.severities).length > 0" id="stats-severities-pie-chart"></div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-6">
          <h3>{{$t('dashboard.sources')}}</h3>
          <ul class="list-group">
            <li v-for="(i,k) in alerts.sources" v-bind:key="k" class="list-group-item">
              <strong>{{k+1}}.</strong>
              {{i.name}}
              <span class="gray">({{i.hits}} {{$t('dashboard.hits')}})</span>
            </li>
          </ul>
          <div v-if="alerts.sources.length == 0" class="alert alert-info alert-dismissable">
            <span class="pficon pficon-info"></span>
            <strong>{{$t('info')}}:</strong>
            {{$t('dashboard.no_data_found')}}.
          </div>
        </div>
        <div class="col-sm-6">
          <h3>{{$t('dashboard.destinations')}}</h3>
          <ul class="list-group">
            <li v-for="(i,k) in alerts.destinations" v-bind:key="k" class="list-group-item">
              <strong>{{k+1}}.</strong>
              {{i.name}}
              <span class="gray">({{i.hits}} {{$t('dashboard.hits')}})</span>
            </li>
          </ul>
          <div v-if="alerts.destinations.length == 0" class="alert alert-info alert-dismissable">
            <span class="pficon pficon-info"></span>
            <strong>{{$t('info')}}:</strong>
            {{$t('dashboard.no_data_found')}}.
          </div>
        </div>
      </div>

      <div class="divider"></div>
      <h2>
        {{$t('dashboard.suricata')}}
        <span v-if="counters.uptime > 0" class="gray min-size right">
          <i18n path="dashboard.last_updated">
            <span place="time">{{counters.uptime | secondsInHour}}</span>
          </i18n>
        </span>
      </h2>
      <div class="row">
        <div class="col-sm-12">
          <h3 v-if="counters.uptime > 0" class="no-mg-top">{{ $t('dashboard.counters') }}</h3>
          <div class="row row-stat">
            <div class="row-inline-block">
              <div v-if="counters.uptime == 0" class="alert alert-info alert-dismissable">
                <span class="pficon pficon-info"></span>
                <strong>{{$t('info')}}:</strong>
                {{$t('dashboard.suricata_is_not_running')}}.
              </div>

              <div
                v-if="counters.uptime > 0"
                class="stats-container col-xs-12 col-sm-6 col-md-2 col-lg-2"
              >
                <span
                  class="card-pf-utilization-card-details-count stats-count" :title="counters.accepted"
                >{{counters.accepted | humanFormat}}</span>
                <span class="card-pf-utilization-card-details-description stats-description">
                  <span
                    class="card-pf-utilization-card-details-line-2 stats-text"
                  >{{$t('dashboard.accepted')}}</span>
                </span>
              </div>
              <div
                v-if="counters.uptime > 0"
                class="stats-container col-xs-12 col-sm-6 col-md-2 col-lg-2"
              >
                <span
                  class="card-pf-utilization-card-details-count stats-count" :title="counters.blocked"
                >{{counters.blocked | humanFormat}}</span>
                <span class="card-pf-utilization-card-details-description stats-description">
                  <span
                    class="card-pf-utilization-card-details-line-2 stats-text"
                  >{{$t('dashboard.blocked')}}</span>
                </span>
              </div>
              <div
                v-if="counters.uptime > 0"
                class="stats-container col-xs-12 col-sm-6 col-md-2 col-lg-2"
              >
                <span
                  class="card-pf-utilization-card-details-count stats-count" :title="counters.replaced"
                >{{counters.replaced | humanFormat}}</span>
                <span class="card-pf-utilization-card-details-description stats-description">
                  <span
                    class="card-pf-utilization-card-details-line-2 stats-text"
                  >{{$t('dashboard.replaced')}}</span>
                </span>
              </div>
              <div
                v-if="counters.uptime > 0"
                class="stats-container col-xs-12 col-sm-6 col-md-2 col-lg-2"
              >
                <span
                  class="card-pf-utilization-card-details-count stats-count" :title="counters.rejected"
                >{{counters.rejected | humanFormat}}</span>
                <span class="card-pf-utilization-card-details-description stats-description">
                  <span
                    class="card-pf-utilization-card-details-line-2 stats-text"
                  >{{$t('dashboard.rejected')}}</span>
                </span>
              </div>
              <div
                v-if="counters.uptime > 0"
                class="stats-container col-xs-12 col-sm-6 col-md-2 col-lg-2"
              >
                <span
                  class="card-pf-utilization-card-details-count stats-count" :title="counters.rules_loaded"
                >{{counters.rules_loaded | humanFormat}}</span>
                <span class="card-pf-utilization-card-details-description stats-description">
                  <span
                    class="card-pf-utilization-card-details-line-2 stats-text"
                  >{{$t('dashboard.rules_loaded')}}</span>
                </span>
              </div>
              <div
                v-if="counters.uptime > 0"
                class="stats-container col-xs-12 col-sm-6 col-md-2 col-lg-2"
              >
                <span
                  class="card-pf-utilization-card-details-count stats-count" :title="counters.rules_failed"
                >{{counters.rules_failed | humanFormat}}</span>
                <span class="card-pf-utilization-card-details-description stats-description">
                  <span
                    class="card-pf-utilization-card-details-line-2 stats-text"
                  >{{$t('dashboard.rules_failed')}}</span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var nethserver = window.nethserver;
var console = window.console;

import generatePieChart from "@/piechart";

export default {
  name: "Dashboard",
  mounted() {
    this.getStats();
  },
  data() {
    return {
      view: {
        isLoaded: false
      },
      alerts: {
        severities: {},
        categories: {},
        sources: [],
        destinations: []
      },
      counters: {},
      eveboxUrl: ""
    };
  },
  methods: {
    initCharts() {
      var context = this;
      var $ = window.jQuery;
      $('[data-toggle="tooltip"]').tooltip();

      if (!this.severitiesPieChart) {
        var names = {};
        var columns = [];
        for (var i in this.alerts && this.alerts.severities) {
          names[i] = i;
          var col = [i, this.alerts && this.alerts.severities[i]];
          columns.push(col);
        }
        this.severitiesPieChart = generatePieChart(
          "#stats-severities-pie-chart",
          {
            names: names,
            columns: columns,
            colors: {
              high: $.pfPaletteColors.red,
              medium: $.pfPaletteColors.orange,
              low: $.pfPaletteColors.yellow
            }
          },
          {
            width: window.innerWidth * 0.4,
            height: 175
          },
          {
            format: {
              value: function(value, ratio, id, index) {
                return value;
              }
            }
          }
        );
      }

      if (!this.categoriesPieChart) {
        var names = {};
        var columns = [];
        for (var i in this.alerts && this.alerts.categories) {
          names[i] = i;
          var col = [i, this.alerts && this.alerts.categories[i]];
          columns.push(col);
        }
        this.categoriesPieChart = generatePieChart(
          "#stats-categories-pie-chart",
          {
            names: names,
            columns: columns
          },
          {
            width: window.innerWidth * 0.5,
            height: 175
          },
          {
            format: {
              value: function(value, ratio, id, index) {
                return value;
              }
            }
          }
        );
      }
    },
    getStats() {
      var context = this;

      context.view.isLoaded = false;
      nethserver.exec(
        ["nethserver-suricata/dashboard/read"],
        {
          action: "statistics",
          hostname: window.location.hostname
        },
        null,
        function(success) {
          try {
            success = JSON.parse(success);
          } catch (e) {
            console.error(e);
          }
          context.alerts = success.alerts;
          context.counters = success.counters;
          context.eveboxUrl = success.url;

          context.initCharts();

          context.view.isLoaded = true;
        },
        function(error) {
          console.error(error);
        }
      );
    }
  }
};
</script>

<style scoped>
.no-mg-top {
  margin-top: 0px !important;
}
.min-size {
  font-size: 14px;
}

.empty-piechart {
  margin-top: 2em;
  text-align: center;
  color: #9c9c9c;
}

.empty-piechart .fa {
  font-size: 92px;
  color: #bbbbbb;
}

.mg-bottom-10 {
  margin-bottom: 10px;
}
</style>
