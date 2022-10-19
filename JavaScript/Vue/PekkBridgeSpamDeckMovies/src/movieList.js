new Vue({
    el: '#movieList',
    data: {
        vsResult: 'win',
        cardList: cardList,
        movieList: movieList,
        filteredMovieList: movieList
    },
    methods: {
        filterList: function () {
            let result = [];
            for(index in this.movieList) {
                if (this.hasCardId(movieList[index].deckParams)) {
                    result.push(movieList[index]);
                }
            }
            this.filteredMovieList = result;
            console.log(result);
        },
        reset: function () {
            this.vsResult = null;
            this.filteredMovieList = this.movieList;
        },
        setVsResult: function (result) {
            this.vsResult = result;
        },
        hasCardId: function (params) {
            //return (params.win == 'a');
            return true; 
        }
    }
});