
try:
	import math
	import sys
	print(sys.version)
	import Orange
	print("Orange")
	import traceback
	import matplotlib.pyplot as plt	
	import os
	from matplotlib.backends.backend_pdf import PdfPages
	#import time 
	#from datetime import date
#except ImportError as err:
#	print(err)
except ModuleNotFoundError as err:
    # Error handling
    print(err)
    exit()
    

def isnan(value):
  try:
      return math.isnan(float(value))
  except:
      return False
      
filename = sys.argv[1]
contents = ''

try:
	with open(filename, 'r') as f:
		contents = f.read()
except IOError:
    print("could not read")

#with open(filename, 'r') as f:
#	contents = f.read()
	
contents_list = contents.split("\n")

#filename_output = os.path.dirname(os.path.abspath(__file__))+'/'
	
if len(sys.argv) > 3:
	format_destine=sys.argv[3]
else:
	format_destine='png'

if len(sys.argv) > 2:
	filename_output=sys.argv[2]
else:
	filename_output=os.path.dirname(os.path.abspath(__file__))+'/example-tmp.'+format_destine

if len(sys.argv) > 4:
	cdmethodbase = int(sys.argv[4]) #'yes' #sys.argv[4]
else:
	cdmethodbase = 1

if len(sys.argv) > 5:
	showrank =1 #'yes' #sys.argv[4]
else:
	showrank=0

try:
	new_path = filename_output
	new_days = open(new_path,'w')
	new_days.write("test")
	new_days.close()
except IOError:
    print("could not write: "+new_path)


try:
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
				list_parameters.append(item)
				#if (item is None) or (str(item).strip()==""): 
				#	t.remove(item)
				#else:	
				#	list_parameters.append(item)
			filename_output1 = list_parameters[0]		
			number_of_datasets = int(list_parameters[1]) - 1		
			cd = Orange.evaluation.compute_CD(list_values, number_of_datasets, alpha='0.05',test='bonferroni-dunn') #tested on 30 datasets
			Orange.evaluation.graph_ranks(list_values, list_names, cd=cd, cdmethod=cdmethodbase, width=6, textspace=0.8, alpha=0.05, nameref=filename_output1, friedmanrank=showrank, n=number_of_datasets)

			#print(filename_output1)
			#print(number_of_datasets)
			#print(list_values)
			#exit()
			
			
			#plt.show()
			
			#Supported formats: emf, eps, pdf, png, ps, raw, rgba, svg, svgz.
			
			#plt.savefig(dir_path_outputs+filename_output+".eps", format='eps', dpi=900)
			#plt.savefig(dir_path_outputs+filename_output+".pdf", format='pdf', dpi=300)
			#plt.savefig(dir_path_outputs+filename_output+".png", format='png', dpi=300, bbox_inches="tight")
			#print("="+format_destine)

			if format_destine == 'multiple':
				f = plt.gcf()  # f = figure(n) if you know the figure number
				f.set_size_inches(11.69,8.27)

				#plt.rc('figure', figsize=(11.69,8.27), dpi=300)

				pdf_pages.savefig()
				plt.close()
			elif format_destine == 'pdf':
				#plt.savefig(dir_path_outputs+filename_output+".pdf", format='pdf', dpi=300)
				plt.savefig(filename_output, format='pdf', dpi=300)
			elif format_destine == 'png':
				#print(filename_output)
				plt.savefig(filename_output, format='png', dpi=300, bbox_inches="tight")
				#plt.savefig(dir_path_outputs+filename_output+".png", format='png', dpi=300, bbox_inches="tight")
			elif format_destine == 'eps':			
				#plt.savefig(dir_path_outputs+filename_output+".eps", format='eps', dpi=300)	
				plt.savefig(filename_output, format='eps', dpi=300)	
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

except IndexError:#Exception:
    #print("could not writess")
    exc_type, exc_value, exc_traceback = sys.exc_info()
    print("*** print_tb:")
    traceback.print_tb(exc_traceback, limit=1, file=sys.stdout)
    print("*** print_exception:")
    # exc_type below is ignored on 3.5 and later
    traceback.print_exception(exc_type, exc_value, exc_traceback,
                              limit=2, file=sys.stdout)
    print("*** print_exc:")
    traceback.print_exc(limit=2, file=sys.stdout)
