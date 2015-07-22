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
NetCommonsApp.controller('PluginManager', function($scope) {

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

});
