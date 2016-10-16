/**
 * @fileoverview PluginManager Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * PluginManager Javascript
 *
 * @param {string} Controller name
 * @param {function($scope)} Controller
 */
NetCommonsApp.controller('PluginManager',
    ['$scope', 'NetCommonsModal', 'NC3_URL', function($scope, NetCommonsModal, NC3_URL) {

      /**
       * Plugins data
       *
       * @type {object}
       */
      $scope.plugins = [];

      /**
       * Plugins map
       *
       * @type {object}
       */
      $scope.pluginsMap = [];

      /**
       * initialize
       *
       * @return {void}
       */
      $scope.initialize = function(data) {
        $scope.plugins = data.plugins;
        $scope.pluginsMap = data.pluginsMap;
      };

      /**
       * move
       *
       * @return {void}
       */
      $scope.move = function(type, direction, index) {
        var dest = (direction === 'up') ? index - 1 : index + 1;
        if (angular.isUndefined($scope.plugins[type][dest])) {
          return false;
        }

        var destPlugin = angular.copy($scope.plugins[type][dest]);
        var targetPlugin = angular.copy($scope.plugins[type][index]);
        $scope.plugins[type][index] = destPlugin;
        $scope.plugins[type][dest] = targetPlugin;
      };

      /**
       * plugins index
       *
       * @return {void}
       */
      $scope.getIndex = function(type, key) {
        return $scope.pluginsMap[type][key];
      };

      /**
       * Show information method
       *
       * @param {number} users.id
       * @return {void}
       */
      $scope.showView = function(type, key) {
        NetCommonsModal.show(
            $scope, 'PluginManagerView',
            NC3_URL + '/plugin_manager/plugin_manager/view/' + type + '/' + key
        );
      };

    }]);


/**
 * User modal controller
 */
NetCommonsApp.controller('PluginManagerView',
    ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {

      /**
       * dialog cancel
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
      };
    }]);
