FNAME = sample

all: pdf

pdf:
	pdflatex -halt-on-error $(FNAME) 
	bibtex $(FNAME) 
	pdflatex -halt-on-error $(FNAME) 

clean:
	rm -f $(FNAME).aux $(FNAME).bbl $(FNAME).blg $(FNAME).log 
	rm -f $(FNAME).lof $(FNAME).lot $(FNAME).toc
	rm -f $(FNAME).ps $(FNAME).dvi
	rm -i $(FNAME).pdf 
	rm -i *~ 
	rm -i *.bak
