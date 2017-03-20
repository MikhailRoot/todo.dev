(function(window,angular,undefined){
    'use strict';

    angular.module('App')
        .component('todo',
            {
                bindings:{
                    todo:'='
                },
                templateUrl:'/clientComponents/Todo.html',
                controller:['$scope','Todos',TodoComponentController],
                controllerAs:'vm'
            }
        );

    function TodoComponentController($scope,Todos){

        var vm=this;

        vm.$onInit=function TodoComponentInit(){
            vm.stateClass=vm.todo.state>0?'finished':'unfinished';
        };

    }

})(window,angular);