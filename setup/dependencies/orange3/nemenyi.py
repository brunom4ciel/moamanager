import Orange
import matplotlib.pyplot as plt
import sys, math
import os
from matplotlib.backends.backend_pdf import PdfPages
import time 
from datetime import date

def isnan(value):
  try:
      return math.isnan(float(value))
  except:
      return False
      
filename = sys.argv[1]

contents = ''
with open(filename, 'r') as f:
	contents = f.read()
	
contents_list = contents.split("\n")

dir_path_outputs = os.path.dirname(os.path.abspath(__file__))+'/outputs/'

if not os.path.exists(dir_path_outputs):
	try:
		os.makedirs(dir_path_outputs)
	except OSError:
		pass
basename_file = os.path.basename(filename)
basename_file = basename_file.split('.')[0]		
dir_path_outputs = dir_path_outputs+'/'+basename_file+'/'

if not os.path.exists(dir_path_outputs):
	try:
		os.makedirs(dir_path_outputs)
	except OSError:
		pass

if len(sys.argv) > 2:
	format_destine=sys.argv[2]
else:
	format_destine='multiple'

if format_destine == 'multiple':
	# The PDF document
	pdf_pages = PdfPages(dir_path_outputs+'multiplefiles-output.pdf')

dir_path_outputs = dir_path_outputs+'/'+format_destine+'/'
if not os.path.exists(dir_path_outputs):
	try:
		os.makedirs(dir_path_outputs)
	except OSError:
		pass		

i=0
n=1
while i < len(contents_list):
	
	if n==1:
		t = contents_list[i].split("\t")
		list_names = []
		for item in t:
			if (item is None) or (str(item).strip()==""): 
				t.remove(item)
			else:	
				list_names.append(item)
	elif n==2:
		t = contents_list[i].split("\t")
		list_values = []
		for item in t:
			if (item is None) or (str(item).strip()==""): 
				t.remove(item)
			else:
				item = item.replace(',', '.')
				list_values.append(float(item))
	else:
		t = contents_list[i].split("\t")
		list_parameters = []
		for item in t:
			if (item is None) or (str(item).strip()==""): 
				t.remove(item)
			else:	
				list_parameters.append(item)
		filename_output = list_parameters[0]		
		number_of_datasets = int(list_parameters[1])
		cd = Orange.evaluation.compute_CD(list_values, number_of_datasets, alpha='0.05',test='nemenyi') #tested on 30 datasets
		Orange.evaluation.graph_ranks(list_values, list_names, cd=cd, width=12, textspace=1, alpha=0.05, nameref=filename_output)

		#plt.show()
		
		#Supported formats: emf, eps, pdf, png, ps, raw, rgba, svg, svgz.
		
		#plt.savefig(dir_path_outputs+filename_output+".eps", format='eps', dpi=900)
		#plt.savefig(dir_path_outputs+filename_output+".pdf", format='pdf', dpi=300)
		#plt.savefig(dir_path_outputs+filename_output+".png", format='png', dpi=300, bbox_inches="tight")
		
		if format_destine == 'multiple':
			f = plt.gcf()  # f = figure(n) if you know the figure number
			f.set_size_inches(11.69,8.27)

			#plt.rc('figure', figsize=(11.69,8.27), dpi=300)

			pdf_pages.savefig()
			plt.close()
		elif format_destine == 'pdf':
			plt.savefig(dir_path_outputs+filename_output+".pdf", format='pdf', dpi=300)
		elif format_destine == 'png':
			plt.savefig(dir_path_outputs+filename_output+".png", format='png', dpi=300, bbox_inches="tight")
		elif format_destine == 'eps':
			
			plt.savefig(dir_path_outputs+filename_output+".eps", format='eps', dpi=300)	
			
		n = 0
				
	i += 1
	n += 1

if format_destine == 'multiple':
	metadata = pdf_pages.infodict()
	metadata['Title'] = 'Nemenyi post-hoc test diagram generate'
	metadata['Author'] = 'Python Script by Bruno M4ciel'
	metadata['Subject'] = 'Nemenyi post-hoc test diagram generate'
	metadata['Keywords'] = 'Nemenyi post-hoc test diagram generate'
    
	# Write the PDF document to the disk
	pdf_pages.close()

#pl.savefig('test.eps', format='eps', dpi=900) 

