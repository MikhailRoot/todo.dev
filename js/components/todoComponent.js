(function(window,angular,undefined){
    'use strict';

    angular.module('AdminApp')
        .component('todo',
            {
                bindings:{
                    todo:'='
                },
                templateUrl:'/components/Todo.html',
                controller:['$scope','Todos',TodoComponentController],
                controllerAs:'vm'
            }
        );

    function TodoComponentController($scope,Todos){

        var vm=this;

        vm.$onInit=function TodoComponentInit(){
            vm.stateClass=vm.todo.state>0?'finished':'unfinished';
        };

        vm.toggleState=function(e){
            if(vm.todo.preview){
                vm.todo.state=vm.todo.state==1?0:1;
                vm.stateClass=vm.todo.state>0?'finished':'unfinished';
                return;
            }

            Todos.toggleState(vm.todo).then(function (result){
                vm.todo=result;
                vm.stateClass=vm.todo.state>0?'finished':'unfinished';
            }).catch(function(err){});
            e.preventDefault();
            e.stopPropagation();
        }


    }

})(window,angular);