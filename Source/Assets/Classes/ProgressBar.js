class ProgressBar
{
    constructor( classElement, min, max )
    {
        this.element = document.querySelector( '.' + classElement );
        this.min = min;
        this.max = max;
        this.current = min;
    }

    exist()
    {
        return this.element != null;
    }

    render()
    {
        this.element.innerHTML = `Обработано ${this.current} из ${this.max}.`;
    }

    next()
    {
        if( this.current < this.max )
          this.current++;
    }

    getCurrent()
    {
       return this.current;
    }

    setMax( max )
    {
        this.max = max;
    }

    setMin( min )
    {
        this.min = min;
        this.current = min;
    }

    getMax()
    {
        return this.max;
    }

    getMin()
    {
        return this.min;
    }
}
