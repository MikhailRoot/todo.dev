(function(window,angular,undefined){
    'use strict';

    angular.module('App')
        .factory('Todos',['$http','$httpParamSerializer',TodosFactory]);

    function TodosFactory($http,$httpParamSerializer){

        // possibly handle here pagination and caching todo list;
        var baseUrl="/todo";

        var listOptions={
            limit:3,
            offset:0,
            currentPage:1,
            maxItemsCount:1, // default fallback
            orderBy: {
                id: 'DESC'
            }
        };
        var todos=[];

        function successHandler(result){
            return result.data;
        }
        function errorHandler(error){
            console.error(error);
            throw error;
        }

        function getTodosList(options){
            // options- has sorting and pagination settings etc.
            if(options)
            {
                listOptions=options;
                todos=[];
            }
            var serialized=$httpParamSerializer(listOptions);
            return $http.get(baseUrl+'?'+serialized).then(function parseTodosListWithPagination(result){

                todos = angular.copy(result.data.items);

                if(result.data.pagination ){
                    if(result.data.pagination.offset>=0){
                        listOptions.offset=result.data.pagination.offset;
                    }

                    if(result.data.pagination.maxItemsCount>=0){
                        listOptions.maxItemsCount=result.data.pagination.maxItemsCount;
                    }

                    if(result.data.pagination.limit>=0){
                        listOptions.limit=result.data.pagination.limit;
                    }
                    if(result.data.pagination.currentPage>=0){
                        listOptions.currentPage=result.data.pagination.currentPage;
                    }

                }

                return todos;

            }).catch(errorHandler);

        }

        function getPageByNumber(page){
            if(page === undefined)
            {page=1;}

            listOptions.currentPage=page;
            //lets calculate  new offset
            listOptions.offset=(page-1)*listOptions.limit;
            return getTodosList(listOptions);
        }
        function getCurrentPaginationOptions(){
            return listOptions;
        }


        function getTodo(id){
            return $http.get(baseUrl+'/'+id).then(successHandler).catch(errorHandler);
        }

        function createTodo(obj){
            return $http.post(baseUrl,obj).then(successHandler).catch(errorHandler);
        }

        //function updateTodo(obj){
        //    return $http.put(baseUrl+'/'+obj.id,obj).then(successHandler).catch(errorHandler);
        //}

        function deleteTodo(obj){
            return $http.delete(baseUrl+'/'+obj.id).then(successHandler).catch(errorHandler);
        }

        //function toggleTodoState(obj){
        //    obj.state=obj.state==0?1:0;
        //    return updateTodo(obj);
        //}

        //function removeTodoPhoto(obj){
        //    obj.photo='';
        //    return updateTodo(obj);
        //}


        return {
            getList:getTodosList,
            getItem:getTodo,
            create:createTodo,
          //  update:updateTodo,
            deleteItem:deleteTodo,
          //  toggleState:toggleTodoState,
          //  removePhoto:removeTodoPhoto,
            getCurrentPaginationOptions:getCurrentPaginationOptions,
            getPageByNumber:getPageByNumber
        }

    }

})(window,angular);